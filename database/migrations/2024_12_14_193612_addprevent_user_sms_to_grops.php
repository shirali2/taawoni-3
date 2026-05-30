<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddpreventUserSmsToGrops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grops', function (Blueprint $table) {
            $table->boolean('prevent_user_sms')->default(0)->nullable();            
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
            $table->dropColumn('prevent_user_sms');
        });
    }
}
