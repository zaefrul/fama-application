<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('party_type')->default('EXPORTER');
            $table->string('party_label')->nullable();
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('parent_id')->nullable();
            $table->string('root_application_id')->nullable();
            $table->string('holder_company_id')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('quantity_remaining')->nullable();
            $table->string('disposition')->default('HOLDING');
            $table->timestamp('sold_at')->nullable();
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropUnique(['application_id']);
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('application_id')->nullable()->change();
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->foreign('application_id')->references('id')->on('export_applications')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('qr_codes')->nullOnDelete();
            $table->foreign('root_application_id')->references('id')->on('export_applications')->cascadeOnDelete();
            $table->foreign('holder_company_id')->references('id')->on('companies')->nullOnDelete();
        });

        DB::statement('
            UPDATE qr_codes
            SET root_application_id = application_id,
                holder_company_id = (
                    SELECT company_id FROM export_applications
                    WHERE export_applications.id = qr_codes.application_id
                ),
                quantity = (
                    SELECT quantity FROM export_applications
                    WHERE export_applications.id = qr_codes.application_id
                ),
                quantity_remaining = (
                    SELECT quantity FROM export_applications
                    WHERE export_applications.id = qr_codes.application_id
                ),
                disposition = \'HOLDING\'
            WHERE application_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['root_application_id']);
            $table->dropForeign(['holder_company_id']);
            $table->dropForeign(['application_id']);
            $table->dropColumn([
                'parent_id',
                'root_application_id',
                'holder_company_id',
                'quantity',
                'quantity_remaining',
                'disposition',
                'sold_at',
            ]);
        });

        DB::table('qr_codes')->whereNull('application_id')->delete();

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('application_id')->nullable(false)->change();
            $table->unique('application_id');
            $table->foreign('application_id')->references('id')->on('export_applications')->cascadeOnDelete();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['party_type', 'party_label']);
        });
    }
};
