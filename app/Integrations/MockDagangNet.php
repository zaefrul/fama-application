<?php

namespace App\Integrations;

use App\Models\Company;

class MockDagangNet
{
    /**
     * @return array<string, mixed>|null
     */
    public function findCompany(string $identifier): ?array
    {
        $company = Company::query()
            ->whereRaw('LOWER(external_account_no) = ?', [strtolower(trim($identifier))])
            ->first();

        if (! $company) {
            return null;
        }

        return [
            'identifier' => $company->external_account_no,
            'registrationNo' => $company->registration_no,
            'name' => $company->name,
            'email' => $company->email,
            'status' => $company->external_status,
            'address' => $company->address,
            'state' => $company->state,
            'district' => $company->district,
            'postcode' => $company->postcode,
            'phone' => $company->phone,
        ];
    }
}
