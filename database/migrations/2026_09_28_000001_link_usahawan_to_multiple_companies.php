<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table) {
            $table->string('user_id');
            $table->string('company_id');
            $table->primary(['user_id', 'company_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        $users = DB::table('users')->whereNotNull('company_id')->get(['id', 'company_id']);
        foreach ($users as $user) {
            if (! DB::table('companies')->where('id', $user->company_id)->exists()) {
                continue;
            }
            DB::table('company_user')->insertOrIgnore([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ]);
        }

        $now = now();
        foreach ($this->demoCompanies($now) as $row) {
            $exists = DB::table('companies')->where('external_account_no', $row['external_account_no'])->exists();
            if (! $exists) {
                DB::table('companies')->insert($row);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function demoCompanies(mixed $now): array
    {
        return [
            [
                'id' => 'co_demo_utara',
                'registration_no' => 'DU30003',
                'external_account_no' => 'H0B00003',
                'name' => 'Ladang Demo Utara Sdn. Bhd.',
                'email' => 'demo.utara@example.com',
                'phone' => '04-111 2233',
                'address' => 'Lot 12, Jalan Ladang, Alor Setar, Kedah',
                'state' => 'Kedah',
                'district' => 'Kota Setar',
                'postcode' => '05000',
                'website' => 'https://demoutara.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'co_demo_selatan',
                'registration_no' => 'SF40004',
                'external_account_no' => 'H0B00004',
                'name' => 'Selatan Fresh Sdn. Bhd.',
                'email' => 'selatan.fresh@example.com',
                'phone' => '07-444 5566',
                'address' => '88, Jalan Segget, Johor Bahru, Johor',
                'state' => 'Johor',
                'district' => 'Johor Bahru',
                'postcode' => '80000',
                'website' => 'https://selatanfresh.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }
};
