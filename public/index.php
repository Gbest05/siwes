<?php

// Front Controller - Entry Point
require_once dirname(__DIR__) . '/app/Core/Session.php';
\App\Core\Session::start();

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
