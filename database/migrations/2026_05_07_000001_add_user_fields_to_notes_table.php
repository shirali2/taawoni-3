<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserFieldsToNotesTable extends Migration
{
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('reminder_date');
            $table->string('visibility')->default('admin_only')->after('is_archived');
            $table->timestamp('seen_by_user_at')->nullable()->after('seen_at');
        });
    }

    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'visibility', 'seen_by_user_at']);
        });
    }
}
