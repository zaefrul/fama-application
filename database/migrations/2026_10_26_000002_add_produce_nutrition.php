<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produce_types', function (Blueprint $table) {
            $table->json('nutrition')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('produce_types', function (Blueprint $table) {
            $table->dropColumn('nutrition');
        });
    }
};
