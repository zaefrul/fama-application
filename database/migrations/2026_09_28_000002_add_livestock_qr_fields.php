<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produce_types', function (Blueprint $table) {
            $table->string('category')->default('PRODUCE')->after('name');
        });

        Schema::table('export_applications', function (Blueprint $table) {
            $table->string('product_kind')->default('PRODUCE')->after('produce_type_id');
            $table->unsignedInteger('head_count')->nullable()->after('quantity_unit');
            $table->date('slaughter_date')->nullable()->after('export_date');
            $table->string('abattoir_name')->nullable()->after('farm_name');
            $table->string('vet_certificate_no')->nullable()->after('coc_number');
        });

        DB::table('produce_types')->insertOrIgnore([
            ['id' => 'pt_lembu', 'name' => 'Lembu', 'category' => 'LIVESTOCK'],
            ['id' => 'pt_kambing', 'name' => 'Kambing', 'category' => 'LIVESTOCK'],
            ['id' => 'pt_ayam', 'name' => 'Ayam', 'category' => 'LIVESTOCK'],
            ['id' => 'pt_kerbau', 'name' => 'Kerbau', 'category' => 'LIVESTOCK'],
        ]);
    }

    public function down(): void
    {
        DB::table('produce_types')->whereIn('id', ['pt_lembu', 'pt_kambing', 'pt_ayam', 'pt_kerbau'])->delete();

        Schema::table('export_applications', function (Blueprint $table) {
            $table->dropColumn(['product_kind', 'head_count', 'slaughter_date', 'abattoir_name', 'vet_certificate_no']);
        });

        Schema::table('produce_types', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
