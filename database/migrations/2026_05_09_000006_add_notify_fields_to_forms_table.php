<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotifyFieldsToFormsTable extends Migration
{
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->boolean('notify_on_submit')->default(false)->after('id');
            $table->string('notify_phone')->nullable()->after('notify_on_submit');
        });
    }

    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['notify_on_submit', 'notify_phone']);
        });
    }
}
