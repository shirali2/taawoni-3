<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExternalFieldsToUserFormTable extends Migration
{
    public function up()
    {
        Schema::table('user_forms', function (Blueprint $table) {
            $table->enum('filled_by', ['user', 'admin', 'external'])->default('user')->after('id');
            $table->unsignedInteger('external_token_id')->nullable()->after('filled_by');
        });
    }

    public function down()
    {
        Schema::table('user_forms', function (Blueprint $table) {
            $table->dropColumn(['filled_by', 'external_token_id']);
        });
    }
}
