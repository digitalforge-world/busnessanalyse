<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('plan', ['free', 'starter', 'pro', 'agency'])->default('free');
            $table->integer('analyses_this_month')->default(0);
            $table->timestamp('analyses_reset_at')->nullable();
            $table->string('locale', 5)->default('fr');
            $table->string('whatsapp_number', 20)->nullable();
            $table->boolean('is_admin')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan', 'analyses_this_month', 'analyses_reset_at', 'locale', 'whatsapp_number', 'is_admin']);
        });
    }
};
