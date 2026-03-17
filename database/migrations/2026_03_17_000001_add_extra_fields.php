<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('url_site')->nullable()->after('nom');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->json('extra_data')->nullable()->after('plan_action');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('url_site');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('extra_data');
        });
    }
};
