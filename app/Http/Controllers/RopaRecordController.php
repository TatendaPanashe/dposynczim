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
            'processing_activity' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:3000'],
            'data_subject_categories' => ['required', 'string', 'max:3000'],
            'personal_data_categories' => ['required', 'string', 'max:3000'],
            'legal_basis' => ['required', 'string', 'max:255'],
            'recipients' => ['nullable', 'string', 'max:3000'],
            'retention_period' => ['required', 'string', 'max:255'],
            'security_measures' => ['required', 'string', 'max:3000'],
            'cross_border_transfer' => ['nullable', 'string', 'max:3000'],
            'owner' => ['required', 'string', 'max:255'],
        ]);

        $record = RopaRecord::create([
            'organization_id' => $request->user()->activeOrganization()?->getKey(),
            'processing_activity' => $validated['processing_activity'],
            'purpose' => $validated['purpose'],
            'data_subject_categories' => $this->lines($validated['data_subject_categories']),
            'personal_data_categories' => $this->lines($validated['personal_data_categories']),
            'legal_basis' => $validated['legal_basis'],
            'recipients' => $this->lines($validated['recipients'] ?? ''),
            'retention_period' => $validated['retention_period'],
            'security_measures' => $this->lines($validated['security_measures']),
            'cross_border_transfer' => $this->lines($validated['cross_border_transfer'] ?? ''),
            'owner' => $validated['owner'],
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
