<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalFormTokensTable extends Migration
{
    public function up()
    {
        Schema::create('external_form_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('form_id');
            $table->string('token', 64)->unique();
            $table->string('password_hash');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by_admin_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_form_tokens');
    }
}
