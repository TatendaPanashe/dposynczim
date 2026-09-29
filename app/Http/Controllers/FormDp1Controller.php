<?php

namespace App\Http\Controllers;

use App\Models\FormDp1;
use App\Models\Organization;
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
        return view('compliance.dp1.create', [
            'organizations' => $this->organizationsFor(auth()->user()),
            'activeOrganization' => auth()->user()->activeOrganization(),
            'form' => null,
        ]);
    }

    public function store(Request $request, PotrazCalculatorService $calculator): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $organization = $this->organizationForRequest($request, $validated);
        $attributes = $this->attributesFromValidated($request, $validated, $organization->id, $calculator);

        $form = FormDp1::create($attributes);

        return to_route('compliance.dp1.show', ['dp1' => $form])->with('success', 'DP1 draft saved securely. Review it before downloading.');
    }

    public function show(FormDp1 $dp1): View
    {
        return view('compliance.dp1.show', ['form' => $dp1]);
    }

    public function edit(FormDp1 $dp1): View
    {
        return view('compliance.dp1.create', [
            'organizations' => $this->organizationsFor(auth()->user()),
            'activeOrganization' => $dp1->organization,
            'form' => $dp1,
        ]);
    }

    public function update(Request $request, FormDp1 $dp1, PotrazCalculatorService $calculator): RedirectResponse
    {
        $validated = $this->validateForm($request, false);
        $organization = $this->organizationForRequest($request, $validated);
        $attributes = $this->attributesFromValidated($request, $validated, $organization->id, $calculator, $dp1);

        $dp1->update($attributes);

        return to_route('compliance.dp1.show', ['dp1' => $dp1])->with('success', 'DP1 draft updated. Review the application before downloading.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateForm(Request $request, bool $requiresCertificate = true): array
    {
        return $request->validate([
            'organization_id' => ['nullable', 'integer'],
            'data_subject_count' => ['required', 'integer', 'min:50'],
            'entity_name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'physical_address' => ['required', 'string', 'max:2000'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'business_sector' => ['required', 'string', 'max:255'],
            'legal_structure' => ['required', 'string', 'max:255'],
            'dpo_name' => ['nullable', 'string', 'max:255'],
            'dpo_phone' => ['nullable', 'string', 'max:255'],
            'dpo_email' => ['nullable', 'email', 'max:255'],
            'representative_name' => ['nullable', 'string', 'max:255'],
            'representative_phone' => ['nullable', 'string', 'max:255'],
            'representative_address' => ['nullable', 'string', 'max:2000'],
            'representative_email' => ['nullable', 'email', 'max:255'],
            'representative_website' => ['nullable', 'string', 'max:255'],
            'data_subject_categories' => ['required', 'string', 'max:5000'],
            'personal_data_types' => ['required', 'string', 'max:5000'],
            'processing_purpose' => ['required', 'string', 'max:5000'],
            'legal_grounds' => ['required', 'string', 'max:5000'],
            'data_recipients' => ['nullable', 'string', 'max:5000'],
            'sensitive_data_details' => ['nullable', 'string', 'max:5000'],
            'processor_details' => ['nullable', 'string', 'max:5000'],
            'cross_border_transfers' => ['nullable', 'string', 'max:5000'],
            'data_risks' => ['nullable', 'string', 'max:5000'],
            'security_measures' => ['required', 'string', 'max:5000'],
            'declarant_name' => ['nullable', 'string', 'max:255'],
            'declarant_position' => ['nullable', 'string', 'max:255'],
            'certificate_of_incorporation' => [$requiresCertificate ? 'required' : 'nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'cr6_cr14' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'tax_clearance' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'data_protection_policy' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'signature_file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function organizationForRequest(Request $request, array $validated): Organization
    {
        $organizationId = (int) ($validated['organization_id'] ?? $request->user()->activeOrganization()?->getKey());
        $organization = $this->organizationsFor($request->user())->firstWhere('id', $organizationId);

        abort_if($organization === null, 403);

        return $organization;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function attributesFromValidated(Request $request, array $validated, int $organizationId, PotrazCalculatorService $calculator, ?FormDp1 $existingForm = null): array
    {
        $fees = $calculator->calculateTierAndFee((int) $validated['data_subject_count']);
        $attachments = array_merge($existingForm?->attachments ?? [], collect([
            'certificate_of_incorporation',
            'cr6_cr14',
            'tax_clearance',
            'data_protection_policy',
            'signature_file',
        ])->filter(fn (string $key): bool => $request->hasFile($key))
            ->mapWithKeys(fn (string $key): array => [
                $key => $request->file($key)->store('dp1-attachments', 'private'),
            ])->all());

        return [
            'organization_id' => $organizationId,
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
                'phone_number' => $validated['phone_number'] ?? null,
                'email_address' => $validated['email_address'] ?? null,
                'website' => $validated['website'] ?? null,
                'business_sector' => $validated['business_sector'],
                'legal_structure' => $validated['legal_structure'],
                'dpo_name' => $validated['dpo_name'] ?? null,
                'dpo_phone' => $validated['dpo_phone'] ?? null,
                'dpo_email' => $validated['dpo_email'] ?? null,
                'representative_name' => $validated['representative_name'] ?? null,
                'representative_phone' => $validated['representative_phone'] ?? null,
                'representative_address' => $validated['representative_address'] ?? null,
                'representative_email' => $validated['representative_email'] ?? null,
                'representative_website' => $validated['representative_website'] ?? null,
                'declarant_name' => $validated['declarant_name'] ?? null,
                'declarant_position' => $validated['declarant_position'] ?? null,
            ],
            'processing_details' => [
                'data_subject_categories' => $validated['data_subject_categories'],
                'personal_data_types' => $validated['personal_data_types'],
                'processing_purpose' => $validated['processing_purpose'],
                'legal_grounds' => $validated['legal_grounds'],
                'data_recipients' => $validated['data_recipients'] ?? null,
            ],
            'sensitive_data_details' => ['details' => $validated['sensitive_data_details'] ?? null],
            'processors' => ['details' => $validated['processor_details'] ?? null],
            'cross_border_transfers' => ['details' => $validated['cross_border_transfers'] ?? null],
            'security_measures' => [
                'risks' => $validated['data_risks'] ?? null,
                'details' => $validated['security_measures'],
            ],
            'attachments' => $attachments,
        ];
    }

    private function organizationsFor($user)
    {
        return $user->isAdmin()
            ? Organization::with('activeDpoAppointment')->orderBy('name')->get()
            : $user->organizations()->with('activeDpoAppointment')->orderBy('name')->get();
    }
}
