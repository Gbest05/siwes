<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class SettingController {
    public function index() {
        Auth::requireAuth();
        $db = Database::getInstance();
        $user = Auth::user();
        $role = Auth::getRole();

        // Get system settings
        $settingsRaw = $db->fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key']] = $s['value'];
        }

        // Get list of backups
        $backupDir = dirname(__DIR__) . '/Database/backups/';
        $backups = [];
        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if (strpos($file, 'siwes_backup_') === 0) {
                    $backups[] = [
                        'filename' => $file,
                        'size' => round(filesize($backupDir . $file) / 1024, 2) . ' KB',
                        'date' => date('Y-m-d H:i:s', filemtime($backupDir . $file))
                    ];
                }
            }
        }

        // Get list of contact messages (Admin / Coordinator only)
        $contactMessages = [];
        $specializedFields = [];
        if (in_array($role, ['admin', 'coordinator'])) {
            $contactMessages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");
            $specializedFields = $db->fetchAll("SELECT * FROM specialized_fields ORDER BY id ASC");
        }

        $response = new Response();
        return $response->render('settings/index', [
            'user' => $user,
            'settings' => $settings,
            'backups' => $backups,
            'contact_messages' => $contactMessages,
            'specialized_fields' => $specializedFields
        ]);
    }

    public function update() {
        Auth::requireAuth();
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();

        if (!$request->isPost()) {
            return $response->redirect('/settings');
        }

        $actionType = $request->post('action_type');

        if ($actionType === 'profile') {
            // Update Profile details
            $email = strtolower(trim($request->post('email')));
            $phone = trim($request->post('phone'));
            $first = trim($request->post('first_name'));
            $last = trim($request->post('last_name'));

            if (empty($email)) {
                Session::setFlash('error', 'Email is required.');
                return $response->redirect('/settings');
            }

            $db->exec("BEGIN TRANSACTION;");
            try {
                // Update User table
                $stmt = $db->prepare("UPDATE users SET email = :e WHERE id = :uid");
                $stmt->bindValue(':e', $email);
                $stmt->bindValue(':uid', $user['id']);
                $stmt->execute();

                // Update Role profile table
                if ($user['role'] === 'student') {
                    $stmtStud = $db->prepare("UPDATE students SET first_name = :f, last_name = :l, phone = :p WHERE user_id = :uid");
                    $stmtStud->bindValue(':f', $first);
                    $stmtStud->bindValue(':l', $last);
                    $stmtStud->bindValue(':p', $phone);
                    $stmtStud->execute();
                } elseif ($user['role'] === 'supervisor') {
                    $stmtSuper = $db->prepare("UPDATE supervisors SET first_name = :f, last_name = :l, phone = :p WHERE user_id = :uid");
                    $stmtSuper->bindValue(':f', $first);
                    $stmtSuper->bindValue(':l', $last);
                    $stmtSuper->bindValue(':p', $phone);
                    $stmtSuper->execute();
                }

                $db->exec("COMMIT;");
                Session::setFlash('success', 'Profile details updated successfully.');
            } catch (\Exception $e) {
                $db->exec("ROLLBACK;");
                Session::setFlash('error', 'Update failed: ' . $e->getMessage());
            }

        } elseif ($actionType === 'security') {
            // Password change
            $currPass = $request->post('current_password');
            $newPass = $request->post('new_password');

            if (empty($currPass) || empty($newPass)) {
                Session::setFlash('error', 'All password fields are required.');
                return $response->redirect('/settings');
            }

            // Verify current password
            $userData = $db->fetchRow("SELECT password_hash FROM users WHERE id = :uid LIMIT 1", [':uid' => $user['id']]);
            if (!password_verify($currPass, $userData['password_hash'])) {
                Session::setFlash('error', 'Current password is incorrect.');
                return $response->redirect('/settings');
            }

            $newHash = password_hash($newPass, PASSWORD_BCRYPT);
            $db->query("UPDATE users SET password_hash = :h WHERE id = :uid", [
                ':h' => $newHash,
                ':uid' => $user['id']
            ]);

            Session::setFlash('success', 'Password updated successfully.');

        } elseif ($actionType === 'system') {
            // System settings (Admin and Coordinator only)
            Auth::requireRole(['admin', 'coordinator']);
            
            $sysName = $request->post('system_name');
            $instName = $request->post('institution_name');
            $session = $request->post('current_session');
            $heroTitle = $request->post('landing_hero_title');
            $heroSubtitle = $request->post('landing_hero_subtitle');
            $contactEmail = $request->post('contact_email');
            $contactPhone = $request->post('contact_phone');
            $matricFormat = $request->post('matric_format');
            $levelOptions = $request->post('level_options');

            // Handle custom logo file upload if provided
            $logoPath = null;
            if (!empty($_FILES['website_logo']['name'])) {
                $file = $_FILES['website_logo'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'svg'];
                
                if (in_array($ext, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
                    $logoDir = dirname(dirname(__DIR__)) . '/public/uploads/logos/';
                    if (!is_dir($logoDir)) {
                        mkdir($logoDir, 0777, true);
                    }
                    $filename = 'logo_' . time() . '.' . $ext;
                    $destPath = $logoDir . $filename;
                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        $logoPath = '/uploads/logos/' . $filename;
                    }
                }
            }

            $db->getConnection()->exec("BEGIN TRANSACTION;");
            try {
                $stmt = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('system_name', :s)");
                $stmt->bindValue(':s', $sysName);
                $stmt->execute();

                $stmt2 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('institution_name', :i)");
                $stmt2->bindValue(':i', $instName);
                $stmt2->execute();

                $stmt3 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('current_session', :se)");
                $stmt3->bindValue(':se', $session);
                $stmt3->execute();

                $stmt4 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('landing_hero_title', :ht)");
                $stmt4->bindValue(':ht', $heroTitle);
                $stmt4->execute();

                $stmt5 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('landing_hero_subtitle', :hs)");
                $stmt5->bindValue(':hs', $heroSubtitle);
                $stmt5->execute();

                $stmt6 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('contact_email', :ce)");
                $stmt6->bindValue(':ce', $contactEmail);
                $stmt6->execute();

                $stmt7 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('contact_phone', :cp)");
                $stmt7->bindValue(':cp', $contactPhone);
                $stmt7->execute();

                $stmt9 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('matric_format', :mf)");
                $stmt9->bindValue(':mf', $matricFormat);
                $stmt9->execute();

                $stmt10 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('level_options', :lo)");
                $stmt10->bindValue(':lo', $levelOptions);
                $stmt10->execute();

                if ($logoPath !== null) {
                    $stmt8 = $db->getConnection()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('logo_path', :lp)");
                    $stmt8->bindValue(':lp', $logoPath);
                    $stmt8->execute();
                }

                $db->getConnection()->exec("COMMIT;");
                Session::setFlash('success', 'System configurations updated successfully.');
            } catch (\Exception $e) {
                $db->getConnection()->exec("ROLLBACK;");
                Session::setFlash('error', 'System update failed: ' . $e->getMessage());
            }
        }

        return $response->redirect('/settings');
    }

    public function backupDb() {
        Auth::requireRole(['admin', 'coordinator']);
        $response = new Response();
        
        $srcPath = dirname(__DIR__) . '/Database/siwes.db';
        $backupDir = dirname(__DIR__) . '/Database/backups/';

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $destPath = $backupDir . 'siwes_backup_' . date('Ymd_His') . '.db';

        if (copy($srcPath, $destPath)) {
            Session::setFlash('success', 'Database backup completed successfully.');
        } else {
            Session::setFlash('error', 'Failed to backup database file.');
        }

        return $response->redirect('/settings');
    }

    public function restoreDb() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/settings');
        }

        $filename = $request->post('filename');
        if (empty($filename)) {
            Session::setFlash('error', 'Backup file selection is required.');
            return $response->redirect('/settings');
        }

        $srcPath = dirname(__DIR__) . '/Database/backups/' . $filename;
        $destPath = dirname(__DIR__) . '/Database/siwes.db';

        if (!file_exists($srcPath)) {
            Session::setFlash('error', 'Selected backup file does not exist.');
            return $response->redirect('/settings');
        }

        // Close active connections before writing to db file
        $db = Database::getInstance();
        $db->getConnection()->close();

        if (copy($srcPath, $destPath)) {
            // Re-instantiate database singleton connection
            Session::setFlash('success', 'Database successfully restored to: ' . $filename);
        } else {
            Session::setFlash('error', 'Database restore failed.');
        }

        return $response->redirect('/settings');
    }

    public function uploadPhoto() {
        Auth::requireAuth();
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();

        if (empty($_FILES['profile_pic']['name'])) {
            Session::setFlash('error', 'Please select a profile picture to upload.');
            return $response->redirect('/settings');
        }

        $file = $_FILES['profile_pic'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            Session::setFlash('error', 'Only JPG, JPEG, and PNG image files are allowed.');
            return $response->redirect('/settings');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Session::setFlash('error', 'Image size must be less than 2MB.');
            return $response->redirect('/settings');
        }

        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = 'profile_' . $user['id'] . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $dbPath = '/uploads/profiles/' . $filename;
            $db->query("UPDATE users SET profile_pic = :p WHERE id = :uid", [
                ':p' => $dbPath,
                ':uid' => $user['id']
            ]);
            Session::setFlash('success', 'Profile photo uploaded successfully.');
        } else {
            Session::setFlash('error', 'Failed to upload photo.');
        }

        return $response->redirect('/settings');
    }

    public function markMessageRead() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/settings');
        }

        $msgId = $request->post('msg_id');
        if (!empty($msgId)) {
            $db->query("UPDATE contact_messages SET status = 'read' WHERE id = :id", [
                ':id' => $msgId
            ]);
            Session::setFlash('success', 'Message marked as read.');
        }

        return $response->redirect('/settings');
    }

    public function updateSpecializedField() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/settings');
        }

        $id = $request->post('field_id');
        $title = trim($request->post('title'));
        $description = trim($request->post('description'));
        $badge = trim($request->post('badge'));
        $imageName = trim($request->post('image_name'));
        $companyName = trim($request->post('company_name'));
        $companyInitials = trim($request->post('company_initials'));
        $rating = (float)$request->post('rating');
        $slotsCount = (int)$request->post('slots_count');
        $status = trim($request->post('status'));

        if (empty($id) || empty($title) || empty($description) || empty($companyName) || empty($companyInitials)) {
            Session::setFlash('error', 'All fields except badge are required.');
            return $response->redirect('/settings');
        }

        try {
            $stmt = $db->getConnection()->prepare("UPDATE specialized_fields SET 
                title = :title, 
                description = :description, 
                badge = :badge, 
                image_name = :image_name, 
                company_name = :company_name, 
                company_initials = :company_initials, 
                rating = :rating, 
                slots_count = :slots_count, 
                status = :status 
                WHERE id = :id");
            
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':badge', $badge);
            $stmt->bindValue(':image_name', $imageName);
            $stmt->bindValue(':company_name', $companyName);
            $stmt->bindValue(':company_initials', $companyInitials);
            $stmt->bindValue(':rating', $rating);
            $stmt->bindValue(':slots_count', $slotsCount);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            Session::setFlash('success', 'Specialized SIWES Field updated successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        return $response->redirect('/settings');
    }
}
