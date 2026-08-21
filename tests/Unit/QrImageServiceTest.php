<?php

namespace Tests\Unit;

use App\Services\QrImageService;
use Tests\TestCase;

class QrImageServiceTest extends TestCase
{
    public function test_png_matches_main_brand_style(): void
    {
        $png = app(QrImageService::class)->png('https://example.test/trace/GPL-QR-000109', 360);

        $this->assertSame("\x89PNG", substr($png, 0, 4));

        $image = imagecreatefromstring($png);
        $this->assertNotFalse($image);
        $this->assertSame(360, imagesx($image));
        $this->assertSame(360, imagesy($image));

        $sample = imagecolorat($image, 180, 180);
        $colors = imagecolorsforindex($image, $sample);
        $this->assertGreaterThan(240, $colors['red']);
        $this->assertGreaterThan(240, $colors['green']);
        $this->assertGreaterThan(240, $colors['blue']);

        $foundBrandGreen = false;
        for ($y = 20; $y < 80; $y += 4) {
            for ($x = 20; $x < 80; $x += 4) {
                $pixel = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                if ($pixel['red'] === 15 && $pixel['green'] === 107 && $pixel['blue'] === 76) {
                    $foundBrandGreen = true;
                    break 2;
                }
            }
        }
        imagedestroy($image);

        $this->assertTrue($foundBrandGreen);
    }

    public function test_pdf_uses_main_label_page_size(): void
    {
        $pdf = app(QrImageService::class)->pdf('https://example.test/trace/GPL-QR-000109', 'GPL-QR-000109', 5);

        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertStringContainsString('/MediaBox [0 0 200.00 260.00]', $pdf);
    }
}
