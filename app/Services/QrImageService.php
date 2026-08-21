<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use FPDF;

class QrImageService
{
    public function png(string $data, int $size = 360): string
    {
        $size = max(160, min(1024, $size));
        $logo = public_path('logos/jejak-gpl.png');

        $builder = new Builder(
            writer: new PngWriter,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 12,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(15, 107, 76),
            backgroundColor: new Color(255, 255, 255),
            logoPath: is_file($logo) ? $logo : '',
            logoResizeToWidth: (int) round($size * 0.22),
            logoPunchoutBackground: true,
        );

        return $builder->build()->getString();
    }

    public function pdf(string $data, string $label, int $sizeCm = 5): string
    {
        $png = $this->png($data, max(256, $sizeCm * 48));
        $tmp = tempnam(sys_get_temp_dir(), 'qr').'.png';
        file_put_contents($tmp, $png);

        $pdf = new FPDF('P', 'mm', [70, 90]);
        $pdf->AddPage();
        $box = min(50, $sizeCm * 10);
        $x = (70 - $box) / 2;
        $pdf->Image($tmp, $x, 18, $box, $box, 'PNG');
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetXY(5, 72);
        $pdf->Cell(60, 8, $label, 0, 0, 'C');
        @unlink($tmp);

        return $pdf->Output('S');
    }

    public function traceUrl(string $qrCode): string
    {
        return rtrim((string) config('app.url'), '/').'/trace/'.$qrCode;
    }
}
