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
        $this->assertSame('0|beta farm resources', $company->nameSortKey());
    }

    public function test_display_name_keeps_plain_company_name(): void
    {
        $company = new Company(['name' => 'ABC Fruits Sdn. Bhd.']);

        $this->assertSame('ABC Fruits Sdn. Bhd.', $company->displayName());
    }

    public function test_numeric_only_names_sort_after_named_companies(): void
    {
        $named = new Company(['name' => 'ZBM AGROTECH']);
        $numbered = new Company(['name' => '26']);

        $this->assertTrue(strcmp($named->nameSortKey(), $numbered->nameSortKey()) < 0);
    }
}
