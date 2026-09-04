<?php

namespace App\Http\Controllers;

use App\Models\FormDp2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormDp2Controller extends Controller
{
    public function index(): View
    {
        return view('compliance.dp2.index', ['appointments' => FormDp2::latest()->get()]);
    }

    public function create(): View
    {
        return view('compliance.dp2.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'controller_name' => ['required', 'string', 'max:255'],
            'controller_license_number' => ['required', 'string', 'max:255'],
            'controller_physical_address' => ['required', 'string', 'max:2000'],
            'controller_postal_address' => ['required', 'string', 'max:1000'],
            'controller_telephone' => ['required', 'string', 'max:50'],
            'controller_fax' => ['nullable', 'string', 'max:50'],
            'controller_email' => ['required', 'email', 'max:255'],
            'business_scope' => ['required', 'string', 'max:3000'],
            'dpo_registration_number' => ['nullable', 'string', 'max:255'],
            'dpo_address' => ['required', 'string', 'max:2000'],
            'qualifications' => ['required', 'string', 'max:3000'],
            'certification_status' => ['required', 'string', 'max:255'],
            'reporting_line' => ['required', 'string', 'max:255'],
            'official_email' => ['required', 'email', 'max:255'],
            'official_phone' => ['required', 'string', 'max:50'],
            'dpo_mobile' => ['required', 'string', 'max:50'],
            'appointment_declaration' => ['required', 'string', 'max:5000'],
            'appointed_at' => ['required', 'date'],
        ]);

        $form = FormDp2::create([
            'organization_id' => $request->user()->activeOrganization()?->getKey(),
            'full_name' => $validated['full_name'],
            'controller_name' => $validated['controller_name'],
            'controller_license_number' => $validated['controller_license_number'],
            'controller_physical_address' => $validated['controller_physical_address'],
            'controller_postal_address' => $validated['controller_postal_address'],
            'controller_telephone' => $validated['controller_telephone'],
            'controller_fax' => $validated['controller_fax'] ?? null,
            'controller_email' => $validated['controller_email'],
            'business_scope' => $validated['business_scope'],
            'dpo_registration_number' => $validated['dpo_registration_number'] ?? null,
            'dpo_address' => $validated['dpo_address'],
            'qualifications' => $this->lines($validated['qualifications']),
            'certification_status' => $validated['certification_status'],
            'reporting_line' => $validated['reporting_line'],
            'official_email' => $validated['official_email'],
            'official_phone' => $validated['official_phone'],
            'dpo_mobile' => $validated['dpo_mobile'],
            'appointment_declaration' => $validated['appointment_declaration'],
            'appointed_at' => $validated['appointed_at'],
            'status' => 'active',
        ]);

        return to_route('compliance.dp2.index')->with('success', 'DPO appointment recorded. Download it when you are ready.');
    }

    /** @return array<int, string> */
    private function lines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
