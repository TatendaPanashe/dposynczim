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
            'organizations' => $request->user()->isAdmin()
                ? Organization::latest()->get()
                : $request->user()->organizations()->latest('organizations.created_at')->get(),
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
            'business_sector' => ['nullable', 'string', 'max:255'],
            'legal_structure' => ['nullable', 'string', 'max:255'],
            'physical_address' => ['nullable', 'string', 'max:2000'],
            'postal_address' => ['nullable', 'string', 'max:2000'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'business_scope' => ['nullable', 'string', 'max:3000'],
        ]);

        $organization = DB::transaction(function () use ($request, $validated): Organization {
            $organization = Organization::create([
                'name' => $validated['name'],
                'registration_number' => $validated['registration_number'] ?? null,
                'business_sector' => $validated['business_sector'] ?? null,
                'legal_structure' => $validated['legal_structure'] ?? null,
                'physical_address' => $validated['physical_address'] ?? null,
                'postal_address' => $validated['postal_address'] ?? null,
                'telephone' => $validated['telephone'] ?? null,
                'fax' => $validated['fax'] ?? null,
                'email' => $validated['email'] ?? null,
                'business_scope' => $validated['business_scope'] ?? null,
                'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(6)),
            ]);

            $request->user()->organizations()->attach($organization->id, ['role' => 'dpo']);

            return $organization;
        });

        $request->session()->put('active_organization_id', $organization->id);

        return to_route('compliance.dp2.create')->with('success', 'Organisation added. Complete its DP2 appointment using your saved DPO details.');
    }

    public function switch(Request $request, Organization $organization): RedirectResponse
    {
        abort_unless(
            $request->user()->isAdmin()
                || $request->user()->organizations()->whereKey($organization->getKey())->exists(),
            403,
        );

        $request->session()->put('active_organization_id', $organization->id);

        return back()->with('success', 'Now viewing '.$organization->name.'.');
    }
}
