<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsHiddenToInvoicePaysTable extends Migration
{
    public function up()
    {
        Schema::table('invoice_pays', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('id');
        });
    }

    public function down()
    {
        Schema::table('invoice_pays', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
}
