<?php

namespace App\Http\Controllers;

use App\Domain\Role;
use App\Integrations\MockDagangNet;
use App\Integrations\MockIfama;
use App\Models\Company;
use App\Models\User;
use App\Services\JejakService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly JejakService $jejak,
        private readonly MockDagangNet $dagangNet,
        private readonly MockIfama $ifama,
    ) {}

    public function showLogin(Request $request): View
    {
        $error = match ($request->query('error')) {
            'invalid' => 'Emel atau kata laluan tidak sah.',
            'role' => 'Peranan yang dipilih tidak sepadan dengan akaun ini.',
            default => $request->query('error'),
        };

        return view('auth.login', [
            'error' => $error,
            'defaultRole' => $request->query('role'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $role = (string) $request->input('role');

        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            return redirect()->route('login', ['error' => 'invalid']);
        }

        $user = $request->user();
        if ($role !== '' && $user->role->value !== $role) {
            Auth::logout();

            return redirect()->route('login', ['error' => 'role']);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            $user->role === Role::FamaOfficer ? route('fama.dashboard') : route('exporter.dashboard')
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegisterExporter(Request $request): View
    {
        $account = trim((string) $request->query('account', ''));
        $error = $request->query('error');
        $message = match ($error) {
            'notfound' => 'Tiada rekod dijumpai'.($account !== '' ? ' untuk '.$account.'.' : '.'),
            'taken' => 'Nombor akaun sudah didaftarkan'.($account !== '' ? ': '.$account.'.' : '.'),
            'email' => 'Emel syarikat pertama sudah digunakan untuk akaun lain.',
            'validation' => 'Sila semak kata laluan dan maklumat pengguna.',
            default => '',
        };

        return view('auth.register-exporter', [
            'error' => $error,
            'errorMessage' => $message,
        ]);
    }

    public function registerExporter(Request $request): RedirectResponse
    {
        $identifiers = collect($request->input('identifiers', []))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => strtolower($value))
            ->values();
        $name = trim((string) $request->input('name'));
        $identity = trim((string) $request->input('identityReference'));
        $password = (string) $request->input('password');
        $confirm = (string) $request->input('confirmPassword');

        if ($identifiers->isEmpty() || $name === '' || $identity === '' || strlen($password) < 8 || $password !== $confirm) {
            return redirect()->route('register.exporter', ['error' => 'validation']);
        }

        $companies = [];
        foreach ($identifiers as $identifier) {
            $companyLookup = $this->dagangNet->findCompany($identifier);
            $match = Company::query()
                ->whereRaw('LOWER(external_account_no) = ?', [strtolower($identifier)])
                ->first();
            if (! $companyLookup || ! $match) {
                return redirect()->route('register.exporter', ['error' => 'notfound', 'account' => $identifier]);
            }
            if ($match->members()->exists() || User::query()->where('company_id', $match->id)->exists()) {
                return redirect()->route('register.exporter', [
                    'error' => 'taken',
                    'account' => $match->external_account_no,
                ]);
            }
            $companies[] = $match;
        }

        $email = $companies[0]->email;
        if (User::query()->where('email', $email)->exists()) {
            return redirect()->route('register.exporter', ['error' => 'email']);
        }

        $user = $this->jejak->createExporterUser([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'identity_reference' => $identity,
            'company_id' => $companies[0]->id,
            'company_ids' => array_map(fn (Company $company) => $company->id, $companies),
        ]);
        Auth::login($user);

        return redirect()->route('exporter.dashboard');
    }

    public function showRegisterFama(Request $request): View
    {
        return view('auth.register-fama', ['error' => $request->query('error')]);
    }

    public function registerFama(Request $request): RedirectResponse
    {
        $identifier = trim((string) $request->input('identifier'));
        $password = (string) $request->input('password');
        $confirm = (string) $request->input('confirmPassword');

        if (strlen($password) < 8 || $password !== $confirm) {
            return redirect()->route('register.fama', ['error' => 'validation']);
        }

        $staff = $this->ifama->findStaff($identifier);
        if (! $staff) {
            return redirect()->route('register.fama', ['error' => 'notfound']);
        }

        $existing = User::query()->where('email', $staff['email'])->first();
        if ($existing) {
            Auth::login($existing);

            return redirect()->route('fama.dashboard');
        }

        $user = $this->jejak->createFamaUser([
            'name' => $staff['fullName'],
            'email' => $staff['email'],
            'password' => $password,
            'identity_reference' => $identifier,
        ]);
        Auth::login($user);

        return redirect()->route('fama.dashboard');
    }

    public function forgotPassword(): View
    {
        return view('auth.forgot-password');
    }
}
