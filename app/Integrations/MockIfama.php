<?php

namespace App\Integrations;

class MockIfama
{
    /**
     * @var list<array{identifier: string, fullName: string, email: string, position: string, active: bool}>
     */
    private const DIRECTORY = [
        [
            'identifier' => '770101145533',
            'fullName' => 'Ali bin Abu Ghani',
            'email' => 'aliabu@fama.gov.my',
            'position' => 'Pengarah Kanan',
            'active' => true,
        ],
        [
            'identifier' => '850909105544',
            'fullName' => 'Noraini binti Hassan',
            'email' => 'noraini@fama.gov.my',
            'position' => 'Pegawai Pemasaran',
            'active' => true,
        ],
    ];

    /**
     * @return array<string, mixed>|null
     */
    public function findStaff(string $identifier): ?array
    {
        foreach (self::DIRECTORY as $row) {
            if ($row['identifier'] === trim($identifier)) {
                return $row;
            }
        }

        return null;
    }
}
