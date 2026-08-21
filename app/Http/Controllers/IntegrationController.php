<?php

namespace App\Http\Controllers;

use App\Integrations\MockDagangNet;
use App\Integrations\MockIfama;
use Illuminate\Http\JsonResponse;

class IntegrationController extends Controller
{
    public function company(string $identifier, MockDagangNet $provider): JsonResponse
    {
        $company = $provider->findCompany($identifier);
        if (! $company) {
            return response()->json(['error' => 'notfound'], 404);
        }

        return response()->json($company);
    }

    public function staff(string $identifier, MockIfama $provider): JsonResponse
    {
        $staff = $provider->findStaff($identifier);
        if (! $staff) {
            return response()->json(['error' => 'notfound'], 404);
        }

        return response()->json($staff);
    }
}
