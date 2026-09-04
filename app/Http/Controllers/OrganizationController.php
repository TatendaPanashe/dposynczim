<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        return view('organizations.index', [
            'organizations' => $request->user()->organizations()->latest('organizations.created_at')->get(),
            'activeOrganization' => $request->user()->activeOrganization(),
        ]);
    }

    public function create(): View
    {
        return view('organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
        ]);

        $organization = DB::transaction(function () use ($request, $validated): Organization {
            $organization = Organization::create([
                'name' => $validated['name'],
                'registration_number' => $validated['registration_number'] ?? null,
                'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(6)),
            ]);

            $request->user()->organizations()->attach($organization->id, ['role' => 'dpo']);

            return $organization;
        });

        $request->session()->put('active_organization_id', $organization->id);

        return to_route('compliance.dashboard')->with('success', 'Organisation added to your DPO workspace.');
    }

    public function switch(Request $request, Organization $organization): RedirectResponse
    {
        abort_unless($request->user()->organizations()->whereKey($organization->getKey())->exists(), 403);

        $request->session()->put('active_organization_id', $organization->id);

        return back()->with('success', 'Now viewing '.$organization->name.'.');
    }
}
