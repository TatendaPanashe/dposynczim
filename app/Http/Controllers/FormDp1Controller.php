<?php

namespace App\Http\Controllers;

use App\Models\FormDp1;
use App\Services\PotrazCalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormDp1Controller extends Controller
{
    public function index(): View
    {
        return view('compliance.dp1.index', ['forms' => FormDp1::latest()->get()]);
    }

    public function create(): View
    {
        return view('compliance.dp1.create');
    }

    public function store(Request $request, PotrazCalculatorService $calculator): RedirectResponse
    {
        $validated = $request->validate([
            'data_subject_count' => ['required', 'integer', 'min:50'],
            'entity_name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'physical_address' => ['required', 'string', 'max:2000'],
            'business_sector' => ['required', 'string', 'max:255'],
            'legal_structure' => ['required', 'string', 'max:255'],
            'data_subject_categories' => ['required', 'string', 'max:5000'],
            'personal_data_types' => ['required', 'string', 'max:5000'],
            'legal_grounds' => ['required', 'string', 'max:5000'],
            'data_recipients' => ['nullable', 'string', 'max:5000'],
            'sensitive_data_details' => ['nullable', 'string', 'max:5000'],
            'processor_details' => ['nullable', 'string', 'max:5000'],
            'cross_border_transfers' => ['nullable', 'string', 'max:5000'],
            'security_measures' => ['required', 'string', 'max:5000'],
            'certificate_of_incorporation' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'cr6_cr14' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'tax_clearance' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'data_protection_policy' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $fees = $calculator->calculateTierAndFee((int) $validated['data_subject_count']);
        $attachments = collect([
            'certificate_of_incorporation',
            'cr6_cr14',
            'tax_clearance',
            'data_protection_policy',
        ])->filter(fn (string $key): bool => $request->hasFile($key))
            ->mapWithKeys(fn (string $key): array => [
                $key => $request->file($key)->store('dp1-attachments', 'private'),
            ])->all();

        $form = FormDp1::create([
            'organization_id' => $request->user()->activeOrganization()?->getKey(),
            'status' => 'draft',
            'data_subject_count' => $validated['data_subject_count'],
            'tier' => $fees['tier'],
            'registration_fee' => $fees['registration_fee'],
            'application_fee' => $fees['application_fee'],
            'total_fee' => $fees['total_cost'],
            'entity_profile' => [
                'entity_name' => $validated['entity_name'],
                'registration_number' => $validated['registration_number'] ?? null,
                'physical_address' => $validated['physical_address'],
                'business_sector' => $validated['business_sector'],
                'legal_structure' => $validated['legal_structure'],
            ],
            'processing_details' => [
                'data_subject_categories' => $validated['data_subject_categories'],
                'personal_data_types' => $validated['personal_data_types'],
                'legal_grounds' => $validated['legal_grounds'],
                'data_recipients' => $validated['data_recipients'] ?? null,
            ],
            'sensitive_data_details' => ['details' => $validated['sensitive_data_details'] ?? null],
            'processors' => ['details' => $validated['processor_details'] ?? null],
            'cross_border_transfers' => ['details' => $validated['cross_border_transfers'] ?? null],
            'security_measures' => ['details' => $validated['security_measures']],
            'attachments' => $attachments,
        ]);

        return to_route('compliance.dp1.index')->with('success', 'DP1 draft saved securely. Download it when you are ready.');
    }
}
