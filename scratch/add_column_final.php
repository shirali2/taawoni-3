<?php

$host = '127.0.0.1';
$db   = 'cp56849_root';
$user = 'cp56849_root';
$pass = 'TkzH0Zo89aQt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("ALTER TABLE invoice_fines ADD COLUMN IF NOT EXISTS total_discount BIGINT DEFAULT 0 AFTER total_paid COMMENT 'جمع کل تخفیف‌های ثبت شده'");
    echo "Column 'total_discount' added successfully (or already exists).\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
