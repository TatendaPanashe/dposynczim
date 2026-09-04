<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Those credentials do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('active_organization_id', $request->user()->activeOrganization()?->getKey());

        return to_route('compliance.dashboard');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        [$user] = DB::transaction(function () use ($validated): array {
            $organization = Organization::create([
                'name' => $validated['organization_name'],
                'slug' => Str::slug($validated['organization_name']).'-'.Str::lower(Str::random(6)),
            ]);

            $user = User::create([
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->organizations()->attach($organization->id, ['role' => 'dpo']);

            return [$user];
        });

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('active_organization_id', $user->organization_id);

        return to_route('compliance.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
