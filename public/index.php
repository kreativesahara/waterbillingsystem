<?php
/**
 * Classic Heights Water Billing & Management System
 * Front Controller Entry Point
 */

declare(strict_types=1);

// PSR-4 compliant autoloader for App\ namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\TenantController;
use App\Controllers\ApiController;

// Start session
Auth::startSession();

// Initialize Database connection (auto-migrates & seeds if needed)
try {
    Database::getConnection();
} catch (\Throwable $e) {
    die("Database Initialization Error: " . $e->getMessage());
}

$router = new Router();

// Root route
$router->get('/', function () {
    if (Auth::check()) {
        if (Auth::isAdmin()) {
            Response::redirect('/admin/dashboard');
        } else {
            Response::redirect('/tenant/dashboard');
        }
    } else {
        Response::redirect('/login');
    }
});

// Authentication Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

// Admin Portal Routes (Guarded by admin middleware)
$router->get('/admin/dashboard', [AdminController::class, 'dashboard'], ['admin']);
$router->get('/admin/meter-entry', [AdminController::class, 'meterEntry'], ['admin']);
$router->get('/admin/records', [AdminController::class, 'records'], ['admin']);
$router->get('/admin/tenants', [AdminController::class, 'tenants'], ['admin']);

// Tenant Portal Routes (Guarded by tenant middleware)
$router->get('/tenant/dashboard', [TenantController::class, 'dashboard'], ['tenant']);
$router->get('/tenant/statement', [TenantController::class, 'statement'], ['tenant']);

// REST API Endpoints
$router->get('/api/calculate', [ApiController::class, 'calculate']);
$router->post('/api/meter-reading', [ApiController::class, 'recordReading']);
$router->post('/api/pay/stk-push', [ApiController::class, 'stkPush']);
$router->post('/api/send-sms', [ApiController::class, 'sendSms']);

// Dispatch request
$router->dispatch();
