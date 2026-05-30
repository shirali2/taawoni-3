<?php

$user = 'root';
$pass = '';

try {
    // Try socket first
    $pdo = new PDO("mysql:unix_socket=/opt/lampp/var/mysql/mysql.sock", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $target_db = '';
    if (in_array('cp56849_root', $databases)) $target_db = 'cp56849_root';
    elseif (in_array('maskan_bimehiran', $databases)) $target_db = 'maskan_bimehiran';
    else {
        // Just take the first non-system DB
        foreach($databases as $d) {
            if (!in_array($d, ['information_schema', 'mysql', 'performance_schema', 'phpmyadmin'])) {
                $target_db = $d;
                break;
            }
        }
    }
    
    if (!$target_db) {
        echo "No suitable database found.\n";
        exit;
    }

    $pdo->exec("USE $target_db");
    $pdo->exec("ALTER TABLE invoice_fines ADD COLUMN IF NOT EXISTS total_discount BIGINT DEFAULT 0 AFTER total_paid COMMENT 'جمع کل تخفیف‌های ثبت شده'");
    echo "Column 'total_discount' added successfully to $target_db.\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
