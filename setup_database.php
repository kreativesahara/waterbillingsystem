<?php
/**
 * Interactive Database Configuration & Diagnostic Tool
 * Water Billing System - Classic Heights Apartments
 */

$db_host = 'localhost';
$db_port = 3306;
$db_user = 'root';
$db_pass = '';
$db_name = 'waterbillingsystem';

$mysql_connected = false;
$db_exists = false;
$tables_status = [];
$error_msg = '';
$success_msg = '';

// Check server connection
try {
    $serverDb = new PDO("mysql:host={$db_host};port={$db_port};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $mysql_connected = true;

    // Check if target database exists
    $stmt = $serverDb->query("SHOW DATABASES LIKE '{$db_name}'");
    $db_exists = (bool) $stmt->fetch();

    // If user clicked initialize / reset
    if (isset($_POST['action']) && $_POST['action'] === 'initialize') {
        $sqlFile = __DIR__ . '/database.sql';
        if (file_exists($sqlFile)) {
            $sqlContent = file_get_contents($sqlFile);
            $serverDb->exec($sqlContent);
            $db_exists = true;
            $success_msg = "Database and tables successfully initialized with sample data!";
        } else {
            $error_msg = "Could not find database.sql in the project root.";
        }
    }

    if ($db_exists) {
        $appDb = new PDO("mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        $expected_tables = ['admins', 'consumers', 'monthly_bill', 'master_bill'];
        foreach ($expected_tables as $tbl) {
            $tStmt = $appDb->query("SHOW TABLES LIKE '{$tbl}'");
            $exists = (bool) $tStmt->fetch();
            $count = 0;
            if ($exists) {
                $cStmt = $appDb->query("SELECT COUNT(*) as cnt FROM `{$tbl}`");
                $cRow = $cStmt->fetch(PDO::FETCH_ASSOC);
                $count = $cRow['cnt'] ?? 0;
            }
            $tables_status[$tbl] = [
                'exists' => $exists,
                'count'  => $count
            ];
        }
    }

} catch (PDOException $e) {
    $error_msg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration | Water Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 40px 15px; }
        .main-card { max-width: 850px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 13px; font-weight: 600; }
        .badge-success-custom { background: #dcfce7; color: #15803d; }
        .badge-danger-custom { background: #fee2e2; color: #b91c1c; }
        .badge-warning-custom { background: #fef3c7; color: #b45309; }
        .code-box { background: #f1f5f9; padding: 12px; border-radius: 8px; font-family: monospace; font-size: 13px; }
    </style>
</head>
<body>

    <div class="main-card">
        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
            <div>
                <h3 class="fw-bold mb-1">Water Billing System &bull; Database Setup</h3>
                <div class="text-secondary small">XAMPP MySQL / MariaDB Environment Configuration</div>
            </div>
            <a href="/waterbillingsystem/home.php" class="btn btn-outline-secondary btn-sm">&larr; Back to App</a>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <div>&#10004; <?= htmlspecialchars($success_msg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger" role="alert">
                <div class="fw-bold mb-1">&#9888; Connection Issue Detected:</div>
                <div class="small"><?= htmlspecialchars($error_msg) ?></div>
                <hr class="my-2">
                <div class="small">
                    <strong>Resolution:</strong> Please open the <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> next to <strong>MySQL</strong>.
                </div>
            </div>
        <?php endif; ?>

        <!-- Server Connection Status -->
        <div class="card mb-4 border">
            <div class="card-body">
                <h5 class="fw-bold mb-3">1. Server &amp; Database Status</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small">MySQL Server Service (Port 3306)</div>
                            <div class="mt-1">
                                <?php if ($mysql_connected): ?>
                                    <span class="status-badge badge-success-custom">&bull; Online &amp; Responding</span>
                                <?php else: ?>
                                    <span class="status-badge badge-danger-custom">&bull; Offline (Stopped in XAMPP)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small">Database (<code><?= htmlspecialchars($db_name) ?></code>)</div>
                            <div class="mt-1">
                                <?php if ($db_exists): ?>
                                    <span class="status-badge badge-success-custom">&bull; Created &amp; Ready</span>
                                <?php else: ?>
                                    <span class="status-badge badge-warning-custom">&bull; Not Found / Uninitialized</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Overview -->
        <?php if ($db_exists): ?>
            <div class="card mb-4 border">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">2. Required Application Tables</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Table Name</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Total Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tables_status as $tableName => $tInfo): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($tableName) ?></code></td>
                                        <td class="small text-secondary">
                                            <?php
                                            switch($tableName) {
                                                case 'admins': echo 'Administrator login credentials'; break;
                                                case 'consumers': echo 'Tenant details and login records'; break;
                                                case 'monthly_bill': echo 'Active billing statements and meters'; break;
                                                case 'master_bill': echo 'Historical billing archive'; break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($tInfo['exists']): ?>
                                                <span class="badge bg-success">Ready</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Missing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format($tInfo['count']) ?> rows</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action / Setup Options -->
        <div class="card mb-4 border">
            <div class="card-body">
                <h5 class="fw-bold mb-2">3. Automatic &amp; Manual Setup Options</h5>
                <p class="text-secondary small mb-3">
                    You can initialize the database using the 1-click button below (once MySQL is started in XAMPP) or import the SQL schema file directly in phpMyAdmin.
                </p>

                <div class="d-flex gap-2 flex-wrap mb-3">
                    <form method="POST" onsubmit="return confirm('Initialize / Reset database with schema and sample data?');">
                        <input type="hidden" name="action" value="initialize">
                        <button type="submit" class="btn btn-primary" <?= !$mysql_connected ? 'disabled' : '' ?>>
                            1-Click Initialize Database
                        </button>
                    </form>
                    <a href="/phpmyadmin/index.php?route=/database/import&db=waterbillingsystem" target="_blank" class="btn btn-outline-secondary">
                        Open phpMyAdmin Import
                    </a>
                    <a href="database.sql" download class="btn btn-outline-secondary">
                        Download database.sql
                    </a>
                </div>

                <div class="code-box">
                    <strong>Schema file location:</strong><br>
                    <code>C:\xampp\htdocs\waterbillingsystem\database.sql</code>
                </div>
            </div>
        </div>

        <!-- Default Credentials Card -->
        <div class="card border">
            <div class="card-body">
                <h5 class="fw-bold mb-2">4. Pre-configured Login Accounts</h5>
                <p class="text-secondary small mb-3">Once initialized, you can test both portals using these accounts:</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <span class="badge bg-primary mb-2">Administrator</span>
                            <div><strong>Phone:</strong> <code>0712345678</code></div>
                            <div><strong>Password:</strong> <code>admin</code></div>
                            <div class="mt-2">
                                <a href="admin/login.php" class="btn btn-sm btn-primary">Login as Admin &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <span class="badge bg-info text-dark mb-2">Tenant (House 1)</span>
                            <div><strong>Phone:</strong> <code>0798765432</code></div>
                            <div><strong>Password:</strong> <code>password</code></div>
                            <div class="mt-2">
                                <a href="user/login.php" class="btn btn-sm btn-info text-white">Login as Tenant &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

