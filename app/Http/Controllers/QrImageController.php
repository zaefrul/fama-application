<?php

namespace App\Http\Controllers;

use App\Services\QrImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QrImageController extends Controller
{
    public function __invoke(Request $request, QrImageService $qr): Response
    {
        $data = (string) $request->query('data', '');
        $size = (int) $request->query('size', 360);
        if ($data === '') {
            return response('missing data', 400);
        }

        return response($qr->png($data, $size), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store',
        ]);
    }
}
