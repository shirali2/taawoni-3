<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatUserAuthLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_auth_logs');
        Schema::create('user_auth_logs', function (Blueprint $table) {
            //code...
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->string('user_code', 20)->nullable();
            $table->integer('grop_id')->nullable();
            $table->string('mobile', 13)->nullable();
            $table->enum('status', ['login', 'logout'])->nullable();
            $table->enum('user_type', ['admin','manager', 'user'])->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('grop_id')->references('id')->on('grops')->onDelete('cascade');
        });

        Schema::table('admins',function(Blueprint $table){
           $table->bigInteger('user_id')->nullable();

           $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins',function(Blueprint $table){ 
            if (Schema::hasColumn('admins', 'user_id')) {
                
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
        Schema::dropIfExists('user_auth_logs');
    }
}
