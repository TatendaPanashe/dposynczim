<?php

namespace App\Http\Controllers;

use App\Models\ComplianceCategory;
use App\Models\ComplianceObligationTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplianceCatalogueController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('compliance.catalogue.index', [
            'templates' => ComplianceObligationTemplate::with('category', 'checklistItems')->orderBy('title')->paginate(30),
            'categories' => ComplianceCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_code' => ['required', 'string', 'max:80', 'unique:compliance_obligation_templates,short_code'],
            'compliance_category_id' => ['nullable', 'integer', 'exists:compliance_categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'regulator' => ['nullable', 'string', 'max:255'],
            'jurisdiction' => ['nullable', 'string', 'max:255'],
            'business_type' => ['nullable', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'in:once-off,weekly,monthly,quarterly,semi-annual,annual,custom'],
            'risk_level' => ['required', 'string', 'in:low,medium,high,critical'],
            'evidence_required' => ['nullable', 'boolean'],
            'legal_reference' => ['nullable', 'string', 'max:255'],
            'reminder_days' => ['nullable', 'string', 'max:255'],
            'checklist_items' => ['nullable', 'string', 'max:3000'],
        ]);

        $template = ComplianceObligationTemplate::create(array_merge($validated, [
            'reminder_days' => collect(explode(',', $validated['reminder_days'] ?? '30,14,7,1'))
                ->map(fn (string $day): int => (int) trim($day))
                ->filter()
                ->values()
                ->all(),
            'evidence_required' => $request->boolean('evidence_required'),
            'is_configurable_template' => true,
        ]));

        foreach (preg_split('/\r\n|\r|\n/', $validated['checklist_items'] ?? '') ?: [] as $index => $title) {
            if (filled($title)) {
                $template->checklistItems()->create(['title' => trim($title), 'sort_order' => $index]);
            }
        }

        return back()->with('success', 'Catalogue template added.');
    }
}
