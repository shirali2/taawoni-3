<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLockUserGroupUsergroupToGropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grops', function (Blueprint $table) {
            $table->boolean('lock_user_group_usergroup')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grops', function (Blueprint $table) {
            $table->dropColumn('lock_user_group_usergroup');
        });
    }
}
