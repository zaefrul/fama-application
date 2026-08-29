<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $keepIds = DB::table('company_produce')
            ->selectRaw('MIN(id) as keep_id')
            ->groupBy('company_id', 'produce_type_id')
            ->pluck('keep_id');

        if ($keepIds->isNotEmpty()) {
            DB::table('company_produce')->whereNotIn('id', $keepIds)->delete();
        }

        Schema::table('company_produce', function (Blueprint $table) {
            $table->unique(['company_id', 'produce_type_id'], 'company_produce_type_unique');
        });
    }

    public function down(): void
    {
        Schema::table('company_produce', function (Blueprint $table) {
            $table->dropUnique('company_produce_type_unique');
        });
    }
};
