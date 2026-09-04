<?php

namespace App\Http\Controllers;

use App\Models\BreachIncident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BreachIncidentController extends Controller
{
    public function index(): View
    {
        return view('compliance.incidents.index', [
            'incidents' => BreachIncident::latest('detected_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('compliance.incidents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'occurred_at' => ['required', 'date'],
            'detected_at' => ['required', 'date'],
            'severity' => ['required', 'in:low,medium,high,critical'],
            'affected_data_subjects' => ['required', 'string', 'max:3000'],
        ]);

        $detectedAt = now()->parse($validated['detected_at']);

        $incident = BreachIncident::create([
            'organization_id' => $request->user()->activeOrganization()?->getKey(),
            'reference' => 'INC-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'occurred_at' => $validated['occurred_at'],
            'detected_at' => $detectedAt,
            'sla_due_at' => $detectedAt->copy()->addHours(24),
            'severity' => $validated['severity'],
            'status' => 'open',
            'affected_data_subjects' => $this->lines($validated['affected_data_subjects']),
        ]);

        return to_route('compliance.incidents.download', $incident)->with('success', 'Incident logged and is ready to download.');
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
