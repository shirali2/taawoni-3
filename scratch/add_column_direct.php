<?php

$env = parse_ini_file(__DIR__.'/../.env');

$host = $env['DB_HOST'] === 'db' ? '127.0.0.1' : $env['DB_HOST'];
$db   = $env['DB_DATABASE'];
$user = $env['DB_USERNAME'];
$pass = $env['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("ALTER TABLE invoice_fines ADD COLUMN IF NOT EXISTS total_discount BIGINT DEFAULT 0 AFTER total_paid COMMENT 'جمع کل تخفیف‌های ثبت شده'");
    echo "Column 'total_discount' added successfully (or already exists).\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
