<?php

namespace App\Http\Controllers\Fama;

use App\Http\Controllers\Controller;
use App\Services\JejakService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(JejakService $jejak): View
    {
        return view('fama.dashboard', ['stats' => $jejak->dashboardFama()]);
    }

    public function menu(): View
    {
        return view('fama.menu');
    }

    public function audit(JejakService $jejak): View
    {
        return view('fama.audit', ['logs' => $jejak->listAudit()]);
    }
}
