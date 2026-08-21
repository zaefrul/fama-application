<?php

namespace App\Services;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use FPDF;
use GdImage;

class QrImageService
{
    /** Prototype QR look. Official FAMA label layout is still an open question. */
    private const DARK = [15, 107, 76];

    private const LIGHT = [255, 255, 255];

    private const MARGIN_MODULES = 2;

    private const LOGO_SCALE = 0.32;

    private const MODULE_GAP = 0.16;

    private const MODULE_RADIUS = 0.38;

    public function png(string $data, int $size = 360): string
    {
        $size = max(160, min(1024, $size));

        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
        );
        $matrix = (new MatrixFactory)->create($qrCode);
        $count = $matrix->getBlockCount();
        $cells = $count + self::MARGIN_MODULES * 2;
        $module = $size / $cells;
        $canvas = max($size, (int) ceil($module * $cells));

        $image = imagecreatetruecolor($canvas, $canvas);
        $dark = imagecolorallocate($image, self::DARK[0], self::DARK[1], self::DARK[2]);
        $light = imagecolorallocate($image, self::LIGHT[0], self::LIGHT[1], self::LIGHT[2]);
        imagefilledrectangle($image, 0, 0, $canvas - 1, $canvas - 1, $light);

        $margin = self::MARGIN_MODULES * $module;
        $drawnFinders = [];

        for ($row = 0; $row < $count; $row++) {
            for ($col = 0; $col < $count; $col++) {
                $finder = $this->finderOrigin($row, $col, $count);
                if ($finder !== null) {
                    $key = $finder[0].','.$finder[1];
                    if (! isset($drawnFinders[$key])) {
                        $drawnFinders[$key] = true;
                        $this->drawFinder($image, $finder[1], $finder[0], $module, $margin, $dark, $light);
                    }

                    continue;
                }

                if ($matrix->getBlockValue($row, $col) !== 1) {
                    continue;
                }

                $gap = $module * self::MODULE_GAP;
                $box = $module - $gap;
                $this->fillRoundedRect(
                    $image,
                    $margin + $col * $module + $gap / 2,
                    $margin + $row * $module + $gap / 2,
                    $box,
                    $box,
                    $box * self::MODULE_RADIUS,
                    $dark,
                );
            }
        }

        $this->compositeLogoPlate($image, $canvas);

        if ($canvas !== $size) {
            $resized = imagecreatetruecolor($size, $size);
            $white = imagecolorallocate($resized, self::LIGHT[0], self::LIGHT[1], self::LIGHT[2]);
            imagefilledrectangle($resized, 0, 0, $size - 1, $size - 1, $white);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, $size, $canvas, $canvas);
            imagedestroy($image);
            $image = $resized;
        }

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    public function pdf(string $data, string $label, int $sizeCm = 5): string
    {
        $png = $this->png($data, max(160, (int) round($sizeCm * 48)));
        $tmp = tempnam(sys_get_temp_dir(), 'qr').'.png';
        file_put_contents($tmp, $png);

        $pdf = new FPDF('P', 'pt', [200, 260]);
        $pdf->AddPage();
        $pdf->Image($tmp, 30, 50, 140, 140, 'PNG');
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetXY(30, 210);
        $pdf->Cell(140, 10, $label, 0, 0, 'L');
        @unlink($tmp);

        return $pdf->Output('S');
    }

    public function traceUrl(string $qrCode): string
    {
        return rtrim((string) config('app.url'), '/').'/trace/'.$qrCode;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private function finderOrigin(int $row, int $col, int $count): ?array
    {
        if ($row < 7 && $col < 7) {
            return [0, 0];
        }
        if ($row < 7 && $col >= $count - 7) {
            return [0, $count - 7];
        }
        if ($row >= $count - 7 && $col < 7) {
            return [$count - 7, 0];
        }

        return null;
    }

    private function drawFinder(
        GdImage $image,
        int $originCol,
        int $originRow,
        float $module,
        float $margin,
        int $dark,
        int $light,
    ): void {
        $x = $margin + $originCol * $module;
        $y = $margin + $originRow * $module;
        $this->fillRoundedRect($image, $x, $y, $module * 7, $module * 7, $module * 1.1, $dark);
        $this->fillRoundedRect($image, $x + $module, $y + $module, $module * 5, $module * 5, $module * 0.7, $light);
        $this->fillRoundedRect($image, $x + $module * 2, $y + $module * 2, $module * 3, $module * 3, $module * 0.45, $dark);
    }

    private function fillRoundedRect(
        GdImage $image,
        float $x,
        float $y,
        float $width,
        float $height,
        float $radius,
        int $color,
    ): void {
        $r = (int) max(0, min($radius, floor(min($width, $height) / 2)));
        $r2 = $r * $r;
        $x1 = (int) round($x);
        $y1 = (int) round($y);
        $x2 = (int) round($x + $width);
        $y2 = (int) round($y + $height);
        $maxX = imagesx($image);
        $maxY = imagesy($image);

        for ($py = $y1; $py < $y2; $py++) {
            for ($px = $x1; $px < $x2; $px++) {
                if ($px < 0 || $py < 0 || $px >= $maxX || $py >= $maxY) {
                    continue;
                }

                $dx = $px < $x1 + $r ? $x1 + $r - $px : ($px >= $x2 - $r ? $px - ($x2 - 1 - $r) : 0);
                $dy = $py < $y1 + $r ? $y1 + $r - $py : ($py >= $y2 - $r ? $py - ($y2 - 1 - $r) : 0);
                $corner = ($px < $x1 + $r || $px >= $x2 - $r) && ($py < $y1 + $r || $py >= $y2 - $r);
                if (! $corner || ($dx * $dx + $dy * $dy) <= $r2) {
                    imagesetpixel($image, $px, $py, $color);
                }
            }
        }
    }

    private function compositeLogoPlate(GdImage $image, int $canvas): void
    {
        $logoPath = public_path('logos/jejak-gpl.png');
        if (! is_file($logoPath)) {
            return;
        }

        $logo = imagecreatefrompng($logoPath);
        if ($logo === false) {
            return;
        }

        $logoBox = (int) round($canvas * self::LOGO_SCALE);
        $pad = (int) round($logoBox * 0.14);
        $inner = max(1, $logoBox - $pad * 2);
        $contained = $this->containImage($logo, $inner);
        imagedestroy($logo);

        $plate = imagecreatetruecolor($logoBox, $logoBox);
        $white = imagecolorallocate($plate, self::LIGHT[0], self::LIGHT[1], self::LIGHT[2]);
        imagefilledrectangle($plate, 0, 0, $logoBox - 1, $logoBox - 1, $white);
        $offsetX = (int) round(($logoBox - imagesx($contained)) / 2);
        $offsetY = (int) round(($logoBox - imagesy($contained)) / 2);
        imagecopy($plate, $contained, $offsetX, $offsetY, 0, 0, imagesx($contained), imagesy($contained));
        imagedestroy($contained);

        $x = (int) round((imagesx($image) - $logoBox) / 2);
        $y = (int) round((imagesy($image) - $logoBox) / 2);
        imagecopy($image, $plate, $x, $y, 0, 0, $logoBox, $logoBox);
        imagedestroy($plate);
    }

    private function containImage(GdImage $source, int $inner): GdImage
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min($inner / $sourceWidth, $inner / $sourceHeight);
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));

        $contained = imagecreatetruecolor($width, $height);
        imagealphablending($contained, false);
        imagesavealpha($contained, true);
        $transparent = imagecolorallocatealpha($contained, 0, 0, 0, 127);
        imagefilledrectangle($contained, 0, 0, $width, $height, $transparent);
        imagealphablending($contained, true);
        imagecopyresampled($contained, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        return $contained;
    }
}
