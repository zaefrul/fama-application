<?php

namespace App\Http\Controllers\Fama;

use App\Http\Controllers\Controller;
use App\Services\JejakService;
use Illuminate\View\View;

class QrController extends Controller
{
    public function __invoke(JejakService $jejak): View
    {
        return view('fama.qr.index', [
            'qrs' => $jejak->listQrCodes(),
        ]);
    }
}
