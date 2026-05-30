<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('manager_id');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('min_threshold', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('manager_id')->references('id')->on('managers')->onDelete('cascade');
            $table->unique('manager_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
