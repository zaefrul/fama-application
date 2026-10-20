<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('pending_holder_company_id')->nullable()->after('holder_company_id');
            $table->timestamp('accepted_at')->nullable()->after('sold_at');
            $table->foreign('pending_holder_company_id')->references('id')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropForeign(['pending_holder_company_id']);
            $table->dropColumn(['pending_holder_company_id', 'accepted_at']);
        });
    }
};
