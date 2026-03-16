<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['gemini_search', 'groq_analysis', 'full'])->default('full');
            $table->longText('analyse_ia')->nullable();
            $table->json('recommandations')->nullable();
            $table->json('plan_action')->nullable();
            $table->enum('statut', ['pending', 'running', 'done', 'failed'])->default('pending');
            $table->string('ia_utilisee')->nullable();
            $table->integer('tokens_utilises')->default(0);
            $table->string('langue', 5)->default('fr');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
