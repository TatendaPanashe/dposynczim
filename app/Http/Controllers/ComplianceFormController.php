<?php

namespace App\Http\Controllers;

use App\Models\ComplianceForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplianceFormController extends Controller
{
    public function index(): View
    {
        return view('compliance.forms.index', [
            'forms' => ComplianceForm::latest('updated_at')->get(),
            'types' => ComplianceForm::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('compliance.forms.create', [
            'form' => null,
            'types' => ComplianceForm::TYPES,
            'statuses' => ComplianceForm::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateForm($request);

        ComplianceForm::create($validated + [
            'organization_id' => $request->user()->activeOrganization()?->getKey(),
        ]);

        return to_route('compliance.forms.index')->with('success', 'Compliance form added to the organisation workspace.');
    }

    public function edit(ComplianceForm $form): View
    {
        return view('compliance.forms.create', [
            'form' => $form,
            'types' => ComplianceForm::TYPES,
            'statuses' => ComplianceForm::STATUSES,
        ]);
    }

    public function update(Request $request, ComplianceForm $form): RedirectResponse
    {
        $form->update($this->validateForm($request));

        return to_route('compliance.forms.index')->with('success', 'Compliance form updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateForm(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', array_keys(ComplianceForm::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(ComplianceForm::STATUSES))],
            'owner' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'effective_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:effective_at'],
            'review_due_at' => ['nullable', 'date'],
            'purpose' => ['nullable', 'string', 'max:5000'],
            'data_subjects' => ['nullable', 'string', 'max:5000'],
            'legal_basis' => ['nullable', 'string', 'max:3000'],
            'authorisation_details' => ['nullable', 'string', 'max:5000'],
            'safeguards' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
