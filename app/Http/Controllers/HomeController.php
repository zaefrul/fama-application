<?php

namespace App\Http\Controllers;

use App\Domain\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user?->role === Role::FamaOfficer) {
            return redirect()->route('fama.dashboard');
        }
        if ($user?->role === Role::Exporter) {
            return redirect()->route('exporter.dashboard');
        }

        return redirect()->route('login');
    }
}
