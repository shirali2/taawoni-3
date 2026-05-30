<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Schema::table('invoice_fines', function (Blueprint $table) {
        if (!Schema::hasColumn('invoice_fines', 'total_discount')) {
            $table->bigInteger('total_discount')->default(0)->after('total_paid')->comment('جمع کل تخفیف‌های ثبت شده');
            echo "Column 'total_discount' added successfully.\n";
        } else {
            echo "Column 'total_discount' already exists.\n";
        }
    });
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
