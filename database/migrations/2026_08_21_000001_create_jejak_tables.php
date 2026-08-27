<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('registration_no');
            $table->string('external_account_no')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('state');
            $table->string('district');
            $table->string('postcode');
            $table->string('website');
            $table->string('logo_path')->nullable();
            $table->string('external_source');
            $table->string('external_status');
            $table->timestamps();
            $table->index('registration_no');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });

        Schema::create('produce_types', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name')->unique();
        });

        Schema::create('company_produce', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('company_id');
            $table->string('produce_type_id');
            $table->string('variety')->nullable();
            $table->boolean('active')->default(true);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('produce_type_id')->references('id')->on('produce_types');
            $table->index('company_id');
            $table->index('produce_type_id');
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('company_id');
            $table->string('type');
            $table->string('certificate_no');
            $table->string('document_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('status');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->index('company_id');
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('company_id');
            $table->string('category');
            $table->string('description');
            $table->string('file_path');
            $table->string('uploaded_by');
            $table->timestamp('uploaded_at');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->index('company_id');
        });

        Schema::create('export_applications', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('application_no')->unique();
            $table->string('company_id');
            $table->string('produce_type_id');
            $table->string('variety');
            $table->string('grade');
            $table->string('size');
            $table->integer('quantity');
            $table->string('quantity_unit');
            $table->string('destination_country');
            $table->string('coc_certificate_id')->nullable();
            $table->string('coc_number');
            $table->date('export_date')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('farm_location')->nullable();
            $table->decimal('farm_lat', 10, 7)->nullable();
            $table->decimal('farm_lng', 10, 7)->nullable();
            $table->string('display_image_path')->nullable();
            $table->string('farm_name');
            $table->string('importer_name');
            $table->text('importer_address');
            $table->string('status');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('produce_type_id')->references('id')->on('produce_types');
            $table->index('company_id');
            $table->index('status');
        });

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('qr_code')->unique();
            $table->string('application_id')->unique();
            $table->string('public_slug')->unique();
            $table->string('status');
            $table->timestamp('generated_at');
            $table->timestamp('activated_at')->nullable();
            $table->foreign('application_id')->references('id')->on('export_applications')->cascadeOnDelete();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('application_id');
            $table->string('officer_user_id');
            $table->string('decision');
            $table->string('remarks');
            $table->timestamp('decided_at');
            $table->foreign('application_id')->references('id')->on('export_applications')->cascadeOnDelete();
            $table->foreign('officer_user_id')->references('id')->on('users');
            $table->index('application_id');
            $table->index('officer_user_id');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('actor_user_id')->nullable();
            $table->string('actor_role');
            $table->string('action');
            $table->string('object_type');
            $table->string('object_id');
            $table->text('before_json')->nullable();
            $table->text('after_json')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['object_type', 'object_id']);
            $table->index('actor_user_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('title');
            $table->string('body');
            $table->boolean('read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('export_applications');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('company_produce');
        Schema::dropIfExists('produce_types');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        Schema::dropIfExists('companies');
    }
};
