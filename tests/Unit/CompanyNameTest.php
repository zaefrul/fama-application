<?php

namespace Tests\Unit;

use App\Models\Company;
use PHPUnit\Framework\TestCase;

class CompanyNameTest extends TestCase
{
    public function test_display_name_strips_leading_list_number(): void
    {
        $company = new Company(['name' => '9 (Beta Farm Resources)']);

        $this->assertSame('Beta Farm Resources', $company->displayName());
        $this->assertSame(9, $company->leadingNumber());
        $this->assertSame('0|000009|beta farm resources', $company->nameSortKey());
    }

    public function test_display_name_strips_number_dash_prefix(): void
    {
        $company = new Company(['name' => '12 - Gold Farm Resources']);

        $this->assertSame('Gold Farm Resources', $company->displayName());
        $this->assertSame(12, $company->leadingNumber());
        $this->assertSame('0|000012|gold farm resources', $company->nameSortKey());
    }

    public function test_display_name_keeps_plain_company_name(): void
    {
        $company = new Company(['name' => 'ABC Fruits Sdn. Bhd.']);

        $this->assertSame('ABC Fruits Sdn. Bhd.', $company->displayName());
        $this->assertNull($company->leadingNumber());
    }

    public function test_leading_numbers_sort_numerically(): void
    {
        $two = new Company(['name' => '2 (Alpha Farm)']);
        $ten = new Company(['name' => '10 (Zebra Orchard)']);
        $plain = new Company(['name' => 'ABC Fruits Sdn. Bhd.']);

        $this->assertTrue(strcmp($two->nameSortKey(), $ten->nameSortKey()) < 0);
        $this->assertTrue(strcmp($ten->nameSortKey(), $plain->nameSortKey()) < 0);
    }
}
