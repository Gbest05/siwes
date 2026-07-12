<?php
use App\Core\Auth;
$role = Auth::getRole();
?>
<div class="space-y-8 animate-fade-in-up" x-data="{ activeTab: 'profile' }">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Portal Settings</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Configure personal preferences, upload documents, and run database tasks.</p>
        </div>
    </div>

    <!-- Layout: Left Menu, Right Contents -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Navigation Menu -->
        <div class="space-y-1 lg:col-span-1 select-none no-print">
            <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-primary text-white' : 'text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="w-full text-left px-4 py-2.5 text-sm font-semibold rounded-xl transition flex items-center space-x-2 focus:outline-none">
                <i class="fa-solid fa-user-circle"></i> <span>Profile Details</span>
            </button>
            
            <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-primary text-white' : 'text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="w-full text-left px-4 py-2.5 text-sm font-semibold rounded-xl transition flex items-center space-x-2 focus:outline-none">
                <i class="fa-solid fa-shield-halved"></i> <span>Password Security</span>
            </button>

            <?php if ($role === 'student'): ?>
                <button @click="activeTab = 'uploads'" :class="activeTab === 'uploads' ? 'bg-primary text-white' : 'text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="w-full text-left px-4 py-2.5 text-sm font-semibold rounded-xl transition flex items-center space-x-2 focus:outline-none">
                    <i class="fa-solid fa-cloud-arrow-up"></i> <span>Document Uploads</span>
                </button>
            <?php endif; ?>

            <?php if (in_array($role, ['admin', 'coordinator'])): ?>
                <button @click="activeTab = 'system'" :class="activeTab === 'system' ? 'bg-primary text-white' : 'text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="w-full text-left px-4 py-2.5 text-sm font-semibold rounded-xl transition flex items-center space-x-2 focus:outline-none">
                    <i class="fa-solid fa-gears"></i> <span>System Config</span>
                </button>
                <button @click="activeTab = 'messages'" :class="activeTab === 'messages' ? 'bg-primary text-white' : 'text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="w-full text-left px-4 py-2.5 text-sm font-semibold rounded-xl transition flex items-center space-x-2 focus:outline-none">
                    <i class="fa-solid fa-envelope-open-text"></i> <span>Contact Messages</span>
                </button>
                <button @click="activeTab = 'database'" :class="activeTab === 'database' ? 'bg-primary text-white' : 'text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="w-full text-left px-4 py-2.5 text-sm font-semibold rounded-xl transition flex items-center space-x-2 focus:outline-none">
                    <i class="fa-solid fa-database"></i> <span>Backup & Restore</span>
                </button>
            <?php endif; ?>
        </div>

        <!-- Contents View -->
        <div class="lg:col-span-3">
            
            <!-- Tab: Profile Details -->
            <div x-show="activeTab === 'profile'" class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 space-y-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white"><i class="fa-solid fa-circle-user mr-2 text-primary"></i> Edit Profile Details</h3>
                
                <!-- Profile Image Upload Form (Available for All Roles) -->
                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="relative w-20 h-20 rounded-2xl overflow-hidden bg-accent text-[#0A0E1A] font-bold flex items-center justify-center text-3xl uppercase shadow-md shrink-0">
                        <?php if (!empty($user['profile_pic'])): ?>
                            <img src="<?= asset($user['profile_pic']) ?>" alt="Profile Photo" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= substr($user['username'], 0, 2) ?>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2 flex-1 text-center sm:text-left">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white">Profile Photo</h4>
                        <p class="text-xs text-slate-400">Upload a recent colored photo (JPG, JPEG, PNG). Max 2MB.</p>
                        <form action="/siwes/settings/upload-photo" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-2 justify-center sm:justify-start items-center">
                            <input type="file" name="profile_pic" accept="image/*" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-xl text-xs" required>
                            <button type="submit" class="bg-primary hover:bg-secondary text-white text-xs px-4 py-2 rounded-xl font-bold transition">Upload Photo</button>
                        </form>
                    </div>
                </div>

                <form action="/siwes/settings/update" method="POST" class="space-y-4">
                    <input type="hidden" name="action_type" value="profile">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['profile']['phone'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        </div>
                    </div>

                    <?php if (in_array($role, ['student', 'supervisor'])): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">First Name</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($user['profile']['first_name']) ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Last Name</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($user['profile']['last_name']) ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="bg-primary hover:bg-secondary text-white text-xs px-6 py-2.5 rounded-xl font-bold transition">Save Profile Changes</button>
                </form>
            </div>

            <!-- Tab: Password Security -->
            <div x-show="activeTab === 'security'" class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 space-y-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white"><i class="fa-solid fa-key mr-2 text-primary"></i> Change Portal Password</h3>
                
                <form action="/siwes/settings/update" method="POST" class="space-y-4">
                    <input type="hidden" name="action_type" value="security">
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Current Password</label>
                        <input type="password" name="current_password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">New Password</label>
                            <input type="password" id="np1" name="new_password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Confirm New Password</label>
                            <input type="password" id="np2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                        </div>
                    </div>

                    <button type="submit" id="sec-btn" class="bg-primary hover:bg-secondary text-white text-xs px-6 py-2.5 rounded-xl font-bold transition">Update Password</button>
                </form>
            </div>

            <!-- Tab: Document Uploads (Students Only) -->
            <?php if ($role === 'student'): ?>
                <div x-show="activeTab === 'uploads'" class="space-y-6">
                    <!-- Passport Card -->
                    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Passport Photograph</h3>
                            <p class="text-xs text-slate-400">Upload a recent colored photo (JPG, JPEG, PNG). Max 2MB.</p>
                            <?php if (!empty($user['profile']['passport_path'])): ?>
                                <a href="<?= $user['profile']['passport_path'] ?>" target="_blank" class="text-xs text-primary font-semibold hover:underline block"><i class="fa-solid fa-image mr-1"></i> View Current Photo</a>
                            <?php endif; ?>
                        </div>
                        
                        <form action="/siwes/student/upload-doc" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-2 items-center">
                            <input type="hidden" name="doc_type" value="passport">
                            <input type="file" name="upload_file" accept="image/*" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-xl text-xs" required>
                            <button type="submit" class="bg-primary hover:bg-secondary text-white text-xs px-4 py-2 rounded-xl font-bold transition">Upload</button>
                        </form>
                    </div>

                    <!-- CV upload -->
                    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Curriculum Vitae (CV)</h3>
                            <p class="text-xs text-slate-400">Upload your CV attachment in PDF format. Max 5MB.</p>
                            <?php if (!empty($user['profile']['cv_path'])): ?>
                                <a href="<?= $user['profile']['cv_path'] ?>" target="_blank" class="text-xs text-primary font-semibold hover:underline block"><i class="fa-solid fa-file-pdf mr-1"></i> View Current CV</a>
                            <?php endif; ?>
                        </div>
                        
                        <form action="/siwes/student/upload-doc" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-2 items-center">
                            <input type="hidden" name="doc_type" value="cv">
                            <input type="file" name="upload_file" accept=".pdf" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-xl text-xs" required>
                            <button type="submit" class="bg-primary hover:bg-secondary text-white text-xs px-4 py-2 rounded-xl font-bold transition">Upload</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tab: System Configurations (Admin / Coordinator only) -->
            <?php if (in_array($role, ['admin', 'coordinator'])): ?>
                <div x-show="activeTab === 'system'" class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 space-y-6">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white"><i class="fa-solid fa-gears mr-2 text-primary"></i> Portal Configurations</h3>
                    
                    <form action="/siwes/settings/update" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="action_type" value="system">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">System Portal Name</label>
                                <input type="text" name="system_name" value="<?= htmlspecialchars($settings['system_name'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Institution Name</label>
                                <input type="text" name="institution_name" value="<?= htmlspecialchars($settings['institution_name'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Current Academic Session</label>
                                <input type="text" name="current_session" value="<?= htmlspecialchars($settings['current_session'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Upload Website Logo</label>
                                <input type="file" name="website_logo" accept="image/*" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-1.5 rounded-xl text-sm focus:outline-none dark:text-white">
                                <?php if (!empty($settings['logo_path'])): ?>
                                    <span class="text-[10px] text-primary dark:text-accent font-semibold mt-1 block">Active Logo: <?= htmlspecialchars(basename($settings['logo_path'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Landing Page Hero Title</label>
                            <input type="text" name="landing_hero_title" value="<?= htmlspecialchars($settings['landing_hero_title'] ?? 'The Future of SIWES Management Starts Here.') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Landing Page Hero Subtitle</label>
                            <textarea name="landing_hero_subtitle" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required><?= htmlspecialchars($settings['landing_hero_subtitle'] ?? 'Access high-quality attachment slots from top corporate experts and secure your SIWES placement. Log daily activities, upload layouts, and track progress. Anytime, anywhere.') ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Contact Email Address</label>
                                <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? 'siwes@university.edu.ng') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Contact Phone Number</label>
                                <input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '+234 803 123 4567') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Matric Number Format Helper</label>
                                <input type="text" name="matric_format" value="<?= htmlspecialchars($settings['matric_format'] ?? 'CSC/2021/045') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Level Option List (format: value:label,value:label)</label>
                                <input type="text" name="level_options" value="<?= htmlspecialchars($settings['level_options'] ?? '3:3rd Year,4:4th Year,5:5th Year') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            </div>
                        </div>

                        <button type="submit" class="bg-primary hover:bg-secondary text-white text-xs px-6 py-2.5 rounded-xl font-bold transition">Save Configs</button>
                    </form>

                    <!-- Specialized SIWES Fields Sub-Panel -->
                    <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800 space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider"><i class="fa-solid fa-briefcase mr-2 text-accent"></i> Edit Specialized SIWES Fields</h3>
                            <p class="text-xs text-slate-400">Modify title descriptions, badges, logos, and slot capacities shown on the public landing page.</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <?php foreach ($specialized_fields as $sf): ?>
                                <div class="bg-slate-50/50 dark:bg-slate-800/20 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 space-y-4">
                                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-slate-100">
                                            <img src="<?= asset('assets/images/' . htmlspecialchars($sf['image_name'])) ?>" alt="" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-800 dark:text-white"><?= htmlspecialchars($sf['title']) ?></h4>
                                            <span class="text-[9px] font-mono text-slate-400">Database ID: #<?= $sf['id'] ?></span>
                                        </div>
                                    </div>

                                    <form action="/siwes/settings/specialized-fields/update" method="POST" class="space-y-3">
                                        <input type="hidden" name="field_id" value="<?= $sf['id'] ?>">

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Field Title</label>
                                                <input type="text" name="title" value="<?= htmlspecialchars($sf['title']) ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white" required>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Badge Text</label>
                                                <input type="text" name="badge" value="<?= htmlspecialchars($sf['badge'] ?? '') ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Description Description</label>
                                            <textarea name="description" rows="2" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white" required><?= htmlspecialchars($sf['description']) ?></textarea>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Company Partner</label>
                                                <input type="text" name="company_name" value="<?= htmlspecialchars($sf['company_name']) ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white" required>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Company Initials</label>
                                                <input type="text" name="company_initials" value="<?= htmlspecialchars($sf['company_initials'] ?? '') ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white" required>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Image File</label>
                                                <select name="image_name" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2 py-1.5 rounded-lg text-xs dark:text-white" required>
                                                    <option value="ui_ux.png" <?= $sf['image_name'] === 'ui_ux.png' ? 'selected' : '' ?>>ui_ux.png (UX/UI)</option>
                                                    <option value="software.png" <?= $sf['image_name'] === 'software.png' ? 'selected' : '' ?>>software.png (Code)</option>
                                                    <option value="fintech.png" <?= $sf['image_name'] === 'fintech.png' ? 'selected' : '' ?>>fintech.png (Fintech)</option>
                                                    <option value="biochem.png" <?= $sf['image_name'] === 'biochem.png' ? 'selected' : '' ?>>biochem.png (Science)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Rating Star</label>
                                                <input type="number" step="0.1" min="1.0" max="5.0" name="rating" value="<?= number_format($sf['rating'] ?? 5.0, 1) ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white" required>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1 font-poppins text-accent">Slots Count</label>
                                                <input type="number" min="0" name="slots_count" value="<?= (int)($sf['slots_count']) ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-lg text-xs dark:text-white" required>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 pt-2">
                                            <div>
                                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                                                <select name="status" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2 py-1.5 rounded-lg text-xs dark:text-white" required>
                                                    <option value="Active" <?= $sf['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="Inactive" <?= $sf['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end">
                                                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white text-[10px] font-bold py-2 rounded-lg transition">Update Field</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab: Backup & Restore (Admin / Coordinator only) -->
                <div x-show="activeTab === 'database'" class="space-y-6">
                    <!-- Backup Action -->
                    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Database Backup copy</h3>
                            <p class="text-xs text-slate-400">Copy the current state of the SQLite database file and save it in backups folder.</p>
                        </div>
                        <form action="/siwes/settings/backup" method="POST">
                            <button type="submit" class="bg-primary hover:bg-secondary text-white text-xs px-6 py-2.5 rounded-xl font-bold transition"><i class="fa-solid fa-copy mr-1"></i> Create Backup File</button>
                        </form>
                    </div>

                    <!-- Available Backups List -->
                    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-file-invoice mr-2 text-accent"></i> Available Backup restore Points</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                                        <th class="py-2">Backup File Name</th>
                                        <th class="py-2">Size</th>
                                        <th class="py-2">Date Created</th>
                                        <th class="py-2 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <?php if (empty($backups)): ?>
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-slate-400">No backup restore points saved.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($backups as $b): ?>
                                            <tr>
                                                <td class="py-2 font-mono text-xs text-slate-800 dark:text-white"><?= htmlspecialchars($b['filename']) ?></td>
                                                <td class="py-2 text-xs"><?= $b['size'] ?></td>
                                                <td class="py-2 text-xs"><?= $b['date'] ?></td>
                                                <td class="py-2 text-right">
                                                    <form action="/siwes/settings/restore" method="POST" onsubmit="return confirm('WARNING: This will overwrite the current active database. Proceed?');">
                                                        <input type="hidden" name="filename" value="<?= $b['filename'] ?>">
                                                        <button type="submit" class="bg-warning/10 hover:bg-warning text-warning hover:text-white px-3 py-1 rounded-lg text-xs font-bold transition">Restore</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tab: Contact Messages (Admin / Coordinator only) -->
            <?php if (in_array($role, ['admin', 'coordinator'])): ?>
                <div x-show="activeTab === 'messages'" class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 space-y-6">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white"><i class="fa-solid fa-inbox mr-2 text-primary"></i> Visitor Contact Messages</h3>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($contact_messages)): ?>
                            <p class="text-sm text-slate-400 text-center py-8">No contact messages received yet.</p>
                        <?php else: ?>
                            <?php foreach ($contact_messages as $msg): ?>
                                <div class="p-4 rounded-2xl border transition hover:shadow-soft <?= $msg['status'] === 'unread' ? 'bg-slate-50/50 border-slate-200 dark:bg-slate-800/40 dark:border-slate-700' : 'bg-white border-slate-150 dark:bg-darkCard dark:border-slate-800' ?>">
                                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold text-sm text-slate-800 dark:text-white"><?= htmlspecialchars($msg['name']) ?></span>
                                                <span class="text-xs text-slate-400">&lt;<?= htmlspecialchars($msg['email']) ?>&gt;</span>
                                                <?php if ($msg['status'] === 'unread'): ?>
                                                    <span class="bg-accent/15 text-[#0A0E1A] dark:text-accent px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase">Unread</span>
                                                <?php endif; ?>
                                            </div>
                                            <h4 class="font-semibold text-xs text-slate-600 dark:text-gray-300 mt-1">Subject: <?= htmlspecialchars($msg['subject']) ?></h4>
                                        </div>
                                        <div class="text-[10px] text-slate-400 shrink-0 font-medium"><?= htmlspecialchars($msg['created_at']) ?></div>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-gray-400 mt-3 border-t border-slate-100 dark:border-slate-800 pt-3 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($msg['message']) ?></div>
                                    
                                    <?php if ($msg['status'] === 'unread'): ?>
                                        <div class="flex justify-end pt-3 mt-3 border-t border-slate-100 dark:border-slate-800">
                                            <form action="/siwes/settings/messages/read" method="POST">
                                                <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                                                <button type="submit" class="bg-primary hover:bg-secondary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg transition">Mark as Read</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="/siwes/public/assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        validatePasswordMatch('np1', 'np2', 'sec-btn');
    });
</script>
