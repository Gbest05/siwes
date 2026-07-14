<?php

// Front Controller - Entry Point
require_once dirname(__DIR__) . '/app/Core/Session.php';
\App\Core\Session::start();

// Graceful check for SQLite3 PHP extension availability
if (!class_exists('SQLite3')) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Configuration Required: SQLite3 Extension Missing</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-6 font-sans">
        <div class="max-w-2xl w-full bg-slate-900 rounded-[28px] border border-slate-800 p-8 shadow-2xl space-y-6">
            <div class="flex items-center space-x-4 border-b border-slate-800 pb-6">
                <div class="w-14 h-14 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold font-poppins">SQLite3 PHP Extension Required</h1>
                    <p class="text-xs text-slate-400 mt-1">Your local PHP environment is missing the SQLite3 driver module.</p>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">How to Fix in XAMPP:</h3>
                <ol class="list-decimal list-inside text-xs text-slate-350 space-y-2.5">
                    <li>Open your <strong class="text-amber-550">XAMPP Control Panel</strong>.</li>
                    <li>Click the <strong class="text-amber-550">Config</strong> button next to <strong class="text-amber-555">Apache</strong>, and choose <strong class="text-white">PHP (php.ini)</strong>.</li>
                    <li>Press <kbd class="bg-slate-800 px-1.5 py-0.5 rounded text-[10px]">Ctrl + F</kbd> and search for <code class="bg-slate-950 px-1.5 py-0.5 rounded font-mono text-amber-400">extension=sqlite3</code>.</li>
                    <li>Remove the semicolon (<code class="text-amber-550">;</code>) from the start of the line so it looks exactly like this:
                        <pre class="bg-slate-950 p-2.5 rounded mt-2 font-mono text-emerald-400">extension=sqlite3
extension=pdo_sqlite</pre>
                    </li>
                    <li>Save the file (<kbd class="bg-slate-800 px-1.5 py-0.5 rounded text-[10px]">Ctrl + S</kbd>).</li>
                    <li>Click <strong class="text-amber-550">Stop</strong> and then <strong class="text-amber-555">Start</strong> on Apache in XAMPP to restart the server.</li>
                </ol>
            </div>

            <div class="space-y-3 pt-4 border-t border-slate-800">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">How to Fix in command line / terminal:</h3>
                <p class="text-xs text-slate-350">Run the PHP server by dynamically loading the extension DLL files:</p>
                <pre class="bg-slate-950 p-3 rounded font-mono text-xs text-amber-400 overflow-x-auto">php -d extension=php_sqlite3.dll -d extension=php_pdo_sqlite.dll -S localhost:8000 -t public</pre>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Global asset path helper
function asset($path) {
    $path = ltrim($path, '/');
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($scriptName, '/siwes') === 0) {
        return '/siwes/public/' . $path;
    }
    return '/' . $path;
}

// Custom PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Move to next autoloader if not in App namespace
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;
use App\Core\Request;

// Instantiate Router
$router = new Router();

// Define Landing and Base Routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->post('/contact', [\App\Controllers\HomeController::class, 'contact']);

// Define Authentication Routes
$router->get('/auth/login', [\App\Controllers\AuthController::class, 'loginView']);
$router->post('/auth/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/auth/register', [\App\Controllers\AuthController::class, 'registerView']);
$router->post('/auth/register', [\App\Controllers\AuthController::class, 'register']);
$router->get('/auth/forgot', [\App\Controllers\AuthController::class, 'forgotView']);
$router->post('/auth/forgot', [\App\Controllers\AuthController::class, 'forgot']);
$router->get('/auth/reset', [\App\Controllers\AuthController::class, 'resetView']);
$router->post('/auth/reset', [\App\Controllers\AuthController::class, 'reset']);
$router->get('/auth/logout', [\App\Controllers\AuthController::class, 'logout']);

// Define Dashboards
$router->get('/admin/dashboard', [\App\Controllers\DashboardController::class, 'admin']);
$router->get('/coordinator/dashboard', [\App\Controllers\DashboardController::class, 'coordinator']);
$router->get('/student/dashboard', [\App\Controllers\DashboardController::class, 'student']);
$router->get('/supervisor/dashboard', [\App\Controllers\DashboardController::class, 'supervisor']);
$router->get('/officer/dashboard', [\App\Controllers\DashboardController::class, 'officer']);

// Student logbook
$router->get('/student/logbook', [\App\Controllers\LogbookController::class, 'studentIndex']);
$router->post('/student/logbook/add', [\App\Controllers\LogbookController::class, 'addLog']);
$router->post('/student/logbook/weekly', [\App\Controllers\LogbookController::class, 'submitWeekly']);

// Supervisor approvals and grading
$router->get('/supervisor/logbooks', [\App\Controllers\LogbookController::class, 'supervisorIndex']);
$router->post('/supervisor/logbook/verify', [\App\Controllers\LogbookController::class, 'verifyLog']);
$router->post('/supervisor/evaluate', [\App\Controllers\LogbookController::class, 'evaluateStudent']);

// Allocation Engine Routes
$router->get('/coordinator/allocations', [\App\Controllers\AllocationController::class, 'index']);
$router->post('/coordinator/allocations/run', [\App\Controllers\AllocationController::class, 'runAutoAllocation']);
$router->post('/coordinator/allocations/assign', [\App\Controllers\AllocationController::class, 'manualAssign']);
$router->post('/coordinator/allocations/lock', [\App\Controllers\AllocationController::class, 'lockAllocation']);

// Management Routes
$router->get('/coordinator/students', [\App\Controllers\StudentController::class, 'index']);
$router->post('/coordinator/student/deactivate', [\App\Controllers\StudentController::class, 'deactivate']);
$router->post('/coordinator/student/import', [\App\Controllers\StudentController::class, 'importCsv']);
$router->post('/coordinator/student/update', [\App\Controllers\StudentController::class, 'update']);
$router->get('/coordinator/users', [\App\Controllers\UserController::class, 'index']);
$router->post('/coordinator/user/suspend', [\App\Controllers\UserController::class, 'suspend']);
$router->post('/coordinator/user/delete', [\App\Controllers\UserController::class, 'delete']);
$router->get('/coordinator/companies', [\App\Controllers\CompanyController::class, 'index']);
$router->post('/coordinator/company/verify', [\App\Controllers\CompanyController::class, 'verify']);
$router->post('/coordinator/company/delete', [\App\Controllers\CompanyController::class, 'delete']);
$router->get('/coordinator/departments', [\App\Controllers\DepartmentController::class, 'index']);
$router->post('/coordinator/department/add', [\App\Controllers\DepartmentController::class, 'add']);
$router->post('/coordinator/department/update', [\App\Controllers\DepartmentController::class, 'update']);
$router->post('/coordinator/department/delete', [\App\Controllers\DepartmentController::class, 'delete']);

// Documents Uploads
$router->post('/student/upload-doc', [\App\Controllers\StudentController::class, 'uploadDoc']);
$router->get('/student/download-letter', [\App\Controllers\StudentController::class, 'downloadLetter']);

// Settings & Utilities
$router->get('/settings', [\App\Controllers\SettingController::class, 'index']);
$router->post('/settings/update', [\App\Controllers\SettingController::class, 'update']);
$router->post('/settings/backup', [\App\Controllers\SettingController::class, 'backupDb']);
$router->post('/settings/restore', [\App\Controllers\SettingController::class, 'restoreDb']);
$router->post('/settings/upload-photo', [\App\Controllers\SettingController::class, 'uploadPhoto']);
$router->post('/settings/messages/read', [\App\Controllers\SettingController::class, 'markMessageRead']);
$router->post('/settings/specialized-fields/update', [\App\Controllers\SettingController::class, 'updateSpecializedField']);

// Reports Module (PDF / Excel Simulated Prints)
$router->get('/coordinator/reports', [\App\Controllers\ReportController::class, 'index']);

// Dispatch HTTP request
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Handle standard base path setups (e.g. if served under /siwes/)
// Strip project folder prefix if it exists in XAMPP htdocs
$basePath = '/siwes';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$router->dispatch($requestUri, $requestMethod);
