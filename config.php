<?php
/**
 * Database Configuration for Water Billing System
 * Compatible with XAMPP MySQL / MariaDB Control Panel
 */

$db_host = 'localhost';
$db_port = 3306;
$db_user = 'root';
$db_pass = '';
$db_name = 'waterbillingsystem';

try {
    // Attempt standard connection to the target database
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $db = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Check if the error is 1049 (Unknown database): Auto-initialize database & schema
    if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
        try {
            // Connect to MySQL server without dbname to create it
            $serverDsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
            $serverDb = new PDO($serverDsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            
            // Create database
            $serverDb->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            
            // Connect to newly created database
            $db = new PDO("mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, $options);
            
            // Import schema from database.sql if available
            $sqlFile = __DIR__ . '/database.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                if (!empty($sqlContent)) {
                    $db->exec($sqlContent);
                }
            }
        } catch (PDOException $initEx) {
            handleDatabaseError($initEx, $db_host, $db_user, $db_name);
        }
    } else {
        handleDatabaseError($e, $db_host, $db_user, $db_name);
    }
}

/**
 * Render friendly diagnostic message when MySQL is stopped or unreachable
 */
function handleDatabaseError($exception, $host, $user, $database) {
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, "Database Connection Error: " . $exception->getMessage() . PHP_EOL);
        return;
    }
    
    // Only render full error page if this is a direct page load, not an AJAX request
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
              (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
              
    if ($isAjax) {
        http_response_code(500);
        echo "Database Connection Error: " . htmlspecialchars($exception->getMessage());
        exit;
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Required | Water Billing System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
            .error-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 30px; max-width: 650px; width: 100%; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); }
            .error-icon { width: 60px; height: 60px; background: #fee2e2; color: #dc2626; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; }
            .step-box { background: #f1f5f9; border-left: 4px solid #0284c7; padding: 12px 16px; border-radius: 6px; margin: 15px 0; font-size: 14px; }
            code { background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #0f172a; }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="error-icon">&#9888;</div>
            <h3 class="fw-bold text-dark mb-2">MySQL Database Connection Required</h3>
            <p class="text-secondary">The Water Billing System was unable to establish a connection to the local MySQL / MariaDB server.</p>
            
            <div class="step-box">
                <strong>How to resolve with XAMPP Control Panel:</strong>
                <ol class="mb-0 mt-2 ps-3">
                    <li>Open the <strong>XAMPP Control Panel</strong> on your desktop.</li>
                    <li>Click the <strong>Start</strong> button next to the <strong>MySQL</strong> module.</li>
                    <li>Ensure the status badge turns <span class="text-success fw-bold">green</span> (running on port 3306).</li>
                    <li>Refresh this page.</li>
                </ol>
            </div>

            <div class="card bg-light border-0 p-3 mb-3" style="font-size: 13px;">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Host:</span>
                    <code><?= htmlspecialchars($host) ?>:3306</code>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Database:</span>
                    <code><?= htmlspecialchars($database) ?></code>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Username:</span>
                    <code><?= htmlspecialchars($user) ?></code>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status details:</span>
                    <span class="text-danger"><?= htmlspecialchars($exception->getMessage()) ?></span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="javascript:location.reload()" class="btn btn-primary px-4">Retry Connection</a>
                <a href="/phpmyadmin/" target="_blank" class="btn btn-outline-secondary">Open phpMyAdmin</a>
                <a href="/dashboard/index.html" class="btn btn-link text-secondary ms-auto">Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

