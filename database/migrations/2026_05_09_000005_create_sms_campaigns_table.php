<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCampaignsTable extends Migration
{
    public function up()
    {
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('manager_id');
            $table->unsignedInteger('campaign_number')->default(1);
            $table->enum('title_type', ['advertising', 'ceo', 'informational', 'warning', 'reminder', 'other']);
            $table->text('message_text');
            $table->string('sender_name')->nullable();
            $table->json('target_user_ids')->nullable();
            $table->json('target_group_ids')->nullable();
            $table->json('target_collection_ids')->nullable();
            $table->boolean('include_active')->default(true);
            $table->boolean('include_inactive')->default(false);
            $table->integer('total_users')->default(0);
            $table->integer('total_sms_per_user')->default(1);
            $table->integer('total_sms_count')->default(0);
            $table->decimal('cost_per_sms', 10, 4)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->integer('interval_seconds')->default(3);
            $table->enum('status', ['draft', 'pending', 'sending', 'sent', 'failed'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->foreign('manager_id')->references('id')->on('managers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_campaigns');
    }
}
