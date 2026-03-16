<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('secteur')->nullable();
            $table->integer('score_digital')->default(0);
            $table->integer('score_croissance')->default(0);
            $table->json('presence_web')->nullable();
            $table->json('points_forts')->nullable();
            $table->text('analyse_comparative')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitors');
    }
};
