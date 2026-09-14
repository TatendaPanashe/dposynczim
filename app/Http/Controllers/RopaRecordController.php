<?php

namespace App\Http\Controllers;

use App\Models\RopaRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RopaRecordController extends Controller
{
    public function index(): View
    {
        return view('compliance.ropa.index', ['records' => RopaRecord::latest()->get()]);
    }

    public function create(): View
    {
        return view('compliance.ropa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_function' => ['nullable', 'string', 'max:255'],
            'storage_location' => ['nullable', 'string', 'max:3000'],
            'processing_activity' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:3000'],
            'data_subject_categories' => ['required', 'string', 'max:3000'],
            'personal_data_categories' => ['required', 'string', 'max:3000'],
            'legal_basis' => ['required', 'string', 'max:255'],
            'controller_name' => ['nullable', 'string', 'max:255'],
            'representative_name' => ['nullable', 'string', 'max:255'],
            'recipients' => ['nullable', 'string', 'max:3000'],
            'retention_period' => ['required', 'string', 'max:255'],
            'retention_basis' => ['nullable', 'string', 'max:3000'],
            'data_classification' => ['nullable', 'string', 'max:255'],
            'processor_name' => ['nullable', 'string', 'max:255'],
            'third_party_agreement' => ['nullable', 'string', 'max:3000'],
            'security_measures' => ['required', 'string', 'max:3000'],
            'cross_border_transfer' => ['nullable', 'string', 'max:3000'],
            'transfer_security_measures' => ['nullable', 'string', 'max:3000'],
            'protection_assessment' => ['nullable', 'string', 'max:3000'],
            'data_collection_method' => ['nullable', 'string', 'max:3000'],
            'consent_evidence' => ['nullable', 'string', 'max:3000'],
            'legitimate_interest_assessment' => ['nullable', 'string', 'max:3000'],
            'data_volume' => ['nullable', 'string', 'max:255'],
            'dpia_record' => ['nullable', 'string', 'max:3000'],
            'data_risks' => ['nullable', 'string', 'max:3000'],
            'risk_impact' => ['nullable', 'string', 'max:255'],
            'data_breaches' => ['nullable', 'string', 'max:3000'],
            'breach_notification' => ['nullable', 'string', 'max:3000'],
            'risk_actions' => ['nullable', 'string', 'max:3000'],
            'action_owner' => ['nullable', 'string', 'max:255'],
            'action_due_date' => ['nullable', 'date'],
            'owner' => ['required', 'string', 'max:255'],
            'reviewed_at' => ['nullable', 'date'],
        ]);

        $record = RopaRecord::create([
            'organization_id' => $request->user()->activeOrganization()?->getKey(),
            'business_function' => $validated['business_function'] ?? null,
            'storage_location' => $validated['storage_location'] ?? null,
            'processing_activity' => $validated['processing_activity'],
            'purpose' => $validated['purpose'],
            'data_subject_categories' => $this->lines($validated['data_subject_categories']),
            'personal_data_categories' => $this->lines($validated['personal_data_categories']),
            'legal_basis' => $validated['legal_basis'],
            'controller_name' => $validated['controller_name'] ?? null,
            'representative_name' => $validated['representative_name'] ?? null,
            'recipients' => $this->lines($validated['recipients'] ?? ''),
            'retention_period' => $validated['retention_period'],
            'retention_basis' => $validated['retention_basis'] ?? null,
            'data_classification' => $validated['data_classification'] ?? null,
            'processor_name' => $validated['processor_name'] ?? null,
            'third_party_agreement' => $validated['third_party_agreement'] ?? null,
            'security_measures' => $this->lines($validated['security_measures']),
            'cross_border_transfer' => $this->lines($validated['cross_border_transfer'] ?? ''),
            'transfer_security_measures' => $validated['transfer_security_measures'] ?? null,
            'protection_assessment' => $validated['protection_assessment'] ?? null,
            'data_collection_method' => $validated['data_collection_method'] ?? null,
            'consent_evidence' => $validated['consent_evidence'] ?? null,
            'legitimate_interest_assessment' => $validated['legitimate_interest_assessment'] ?? null,
            'data_volume' => $validated['data_volume'] ?? null,
            'dpia_record' => $validated['dpia_record'] ?? null,
            'data_risks' => $validated['data_risks'] ?? null,
            'risk_impact' => $validated['risk_impact'] ?? null,
            'data_breaches' => $validated['data_breaches'] ?? null,
            'breach_notification' => $validated['breach_notification'] ?? null,
            'risk_actions' => $validated['risk_actions'] ?? null,
            'action_owner' => $validated['action_owner'] ?? null,
            'action_due_date' => $validated['action_due_date'] ?? null,
            'owner' => $validated['owner'],
            'reviewed_at' => $validated['reviewed_at'] ?? null,
        ]);

        return to_route('compliance.ropa.index')->with('success', 'Processing activity added to the register.');
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
