<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('export_applications', 'lot_no')) {
                $table->string('lot_no')->nullable();
            }
            if (! Schema::hasColumn('export_applications', 'farm_location')) {
                $table->string('farm_location')->nullable();
            }
            if (! Schema::hasColumn('export_applications', 'farm_lat')) {
                $table->decimal('farm_lat', 10, 7)->nullable();
            }
            if (! Schema::hasColumn('export_applications', 'farm_lng')) {
                $table->decimal('farm_lng', 10, 7)->nullable();
            }
            if (! Schema::hasColumn('export_applications', 'display_image_path')) {
                $table->string('display_image_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('export_applications', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['lot_no', 'farm_location', 'farm_lat', 'farm_lng', 'display_image_path'],
                fn (string $column) => Schema::hasColumn('export_applications', $column)
            ));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
