<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_accesses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('qr_id');
            $table->string('qr_code');
            $table->timestamp('accessed_at');
            $table->foreign('qr_id')->references('id')->on('qr_codes')->cascadeOnDelete();
            $table->index('accessed_at');
            $table->index(['qr_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_accesses');
    }
};
