<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('secteur')->nullable();
            $table->string('pays')->nullable();
            $table->string('langue_detectee', 5)->default('fr');
            $table->text('description')->nullable();
            $table->year('annee_fondation')->nullable();
            $table->enum('taille', ['TPE', 'PME', 'Grande entreprise'])->nullable();
            $table->integer('score_digital')->default(0);
            $table->integer('score_croissance')->default(0);
            $table->json('presence_web')->nullable();
            $table->json('points_forts')->nullable();
            $table->json('points_faibles')->nullable();
            $table->json('opportunites')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
