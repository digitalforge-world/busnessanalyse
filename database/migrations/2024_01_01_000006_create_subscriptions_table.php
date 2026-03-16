<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['free', 'starter', 'pro', 'agency'])->default('free');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('cinetpay_transaction_id')->nullable();
            $table->enum('statut', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamp('expire_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
