<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_applications', function (Blueprint $table) {
            $table->string('market')->default('EXPORT')->after('product_kind');
        });
    }

    public function down(): void
    {
        Schema::table('export_applications', function (Blueprint $table) {
            $table->dropColumn('market');
        });
    }
};
