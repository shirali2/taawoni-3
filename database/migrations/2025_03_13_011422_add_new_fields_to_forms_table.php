<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            // فیلد عدم نمایش به مدیر
            $table->boolean('no_display_manager')->default(1)->nullable();
            // فیلد عدم امکان نمایش فرم توسط مدیر
            $table->boolean('unable_manager_edit')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('no_display_manager');
            $table->dropColumn('unable_manager_edit');
        });
    }
}
