<?php

namespace App\Http\Controllers;

use App\Models\ComplianceCategory;
use App\Models\ComplianceObligationTemplate;
use App\Models\OrganisationComplianceObligation;
use App\Models\User;
use App\Services\ComplianceRecurrenceService;
use App\Services\DataProtectionComplianceFeed;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComplianceCalendarController extends Controller
{
    public function index(Request $request, DataProtectionComplianceFeed $feed): View
    {
        $month = CarbonImmutable::parse($request->input('month', today()->format('Y-m-01')))->startOfMonth();
        $calendarStart = $month->startOfWeek();
        $calendarEnd = $month->endOfMonth()->endOfWeek();
        $obligations = $this->filteredObligations($request)
            ->with(['assignedUser', 'reviewer', 'checklistItems', 'evidence'])
            ->orderBy('due_at')
            ->paginate(20)
            ->withQueryString();
        $monthObligations = $this->filteredObligations($request)
            ->with('assignedUser')
            ->whereBetween('due_at', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->orderBy('due_at')
            ->get();
        $activeQuery = $this->filteredObligations($request)->whereNotIn('status', ['completed', 'waived']);
        $periodDue = $this->filteredObligations($request)
            ->whereBetween('due_at', [$month->toDateString(), $month->endOfMonth()->toDateString()])
            ->where('status', '!=', 'waived');
        $periodDueCount = (clone $periodDue)->count();
        $completedOnTime = (clone $periodDue)->where('status', 'completed')->whereColumn('completed_at', '<=', 'due_at')->count();

        $organization = $request->user()->activeOrganization();

        return view('compliance.calendar.index', [
            'obligations' => $obligations,
            'calendarDays' => collect(CarbonPeriod::create($calendarStart, $calendarEnd))->map(fn ($date): CarbonImmutable => CarbonImmutable::parse($date)),
            'calendarMonth' => $month,
            'monthObligations' => $monthObligations->groupBy(fn (OrganisationComplianceObligation $obligation): string => $obligation->due_at->toDateString()),
            'calendarStats' => [
                'active' => (clone $activeQuery)->count(),
                'dueToday' => (clone $activeQuery)->whereDate('due_at', today())->count(),
                'due7' => (clone $activeQuery)->whereBetween('due_at', [today(), today()->addDays(7)])->count(),
                'overdue' => (clone $activeQuery)->whereDate('due_at', '<', today())->count(),
                'score' => $periodDueCount > 0 ? round(($completedOnTime / $periodDueCount) * 100) : 100,
            ],
            'todayActions' => $this->filteredObligations($request)->with('assignedUser')->whereDate('due_at', '<=', today())->whereNotIn('status', ['completed', 'waived'])->orderBy('due_at')->limit(6)->get(),
            'upcomingCritical' => $this->filteredObligations($request)->with('assignedUser')->whereIn('risk_level', ['high', 'critical'])->whereDate('due_at', '>=', today())->whereNotIn('status', ['completed', 'waived'])->orderBy('due_at')->limit(6)->get(),
            'templates' => ComplianceObligationTemplate::with('category')->where('is_active', true)->orderBy('title')->get(),
            'categories' => ComplianceCategory::where('is_active', true)->orderBy('name')->get(),
            'users' => $this->usersFor($request->user()),
            'canManageWorkspace' => $request->user()->canManageComplianceWorkspace(),
            'feedItems' => $organization ? $feed->linkedItemsFor($organization) : collect(),
            'filters' => $request->only(['view', 'category', 'status', 'risk_level', 'owner', 'regulator', 'from', 'to']),
        ]);
    }

    public function show(OrganisationComplianceObligation $obligation): View
    {
        abort_unless($obligation->isVisibleTo(auth()->user()), 403);

        return view('compliance.calendar.show', [
            'obligation' => $obligation->load(['assignedUser', 'reviewer', 'checklistItems', 'evidence', 'activityLogs']),
            'users' => $this->usersFor(auth()->user()),
            'canManageWorkspace' => auth()->user()->canManageComplianceWorkspace(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageComplianceWorkspace(), 403);

        $validated = $request->validate([
            'template_id' => ['nullable', 'integer', 'exists:compliance_obligation_templates,id'],
            'title' => ['required_without:template_id', 'nullable', 'string', 'max:255'],
            'due_at' => ['required', 'date'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'reviewer_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'frequency' => ['nullable', 'string', 'in:once-off,weekly,monthly,quarterly,semi-annual,annual,custom'],
            'risk_level' => ['nullable', 'string', 'in:low,medium,high,critical'],
            'category' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $organization = $request->user()->activeOrganization();
        abort_if($organization === null, 403);
        $this->authorizeAssignedUsers($request, $validated);

        $template = isset($validated['template_id'])
            ? ComplianceObligationTemplate::with('category', 'checklistItems')->findOrFail($validated['template_id'])
            : null;

        $obligation = DB::transaction(function () use ($validated, $organization, $template, $request): OrganisationComplianceObligation {
            $obligation = OrganisationComplianceObligation::create([
                'organization_id' => $organization->getKey(),
                'compliance_obligation_template_id' => $template?->getKey(),
                'assigned_user_id' => $validated['assigned_user_id'] ?? null,
                'reviewer_user_id' => $validated['reviewer_user_id'] ?? null,
                'title' => $template?->title ?? $validated['title'],
                'short_code' => $template?->short_code,
                'description' => $template?->description,
                'category' => $template?->category?->name ?? $validated['category'] ?? 'Internal',
                'regulator' => $template?->regulator,
                'jurisdiction' => $template?->jurisdiction ?? 'Zimbabwe',
                'frequency' => $validated['frequency'] ?? $template?->frequency ?? 'once-off',
                'reminder_days' => $template?->reminder_days ?? [30, 14, 7, 1],
                'evidence_required' => $template?->evidence_required ?? true,
                'risk_level' => $validated['risk_level'] ?? $template?->risk_level ?? 'medium',
                'legal_reference' => $template?->legal_reference,
                'due_at' => $validated['due_at'],
                'first_due_at' => $validated['due_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($template?->checklistItems ?? [] as $item) {
                $obligation->checklistItems()->create(['title' => $item->title, 'sort_order' => $item->sort_order]);
            }

            $this->log($obligation, 'created', $request->user(), ['template_id' => $template?->getKey()]);

            return $obligation;
        });

        return to_route('compliance.calendar.show', $obligation)->with('success', 'Compliance obligation added to the calendar.');
    }

    public function update(Request $request, OrganisationComplianceObligation $obligation): RedirectResponse
    {
        abort_unless($obligation->isVisibleTo($request->user()), 403);
        abort_unless($request->user()->canManageComplianceWorkspace(), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', OrganisationComplianceObligation::STATUSES)],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'reviewer_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $this->authorizeAssignedUsers($request, $validated);

        $obligation->update($validated);
        $this->log($obligation, 'updated', $request->user(), $validated);

        return back()->with('success', 'Compliance obligation updated.');
    }

    public function complete(Request $request, OrganisationComplianceObligation $obligation, ComplianceRecurrenceService $recurrence): RedirectResponse
    {
        abort_unless($obligation->isVisibleTo($request->user()), 403);

        $validated = $request->validate([
            'evidence' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($obligation->evidence_required && ! $request->hasFile('evidence') && $obligation->evidence()->doesntExist()) {
            return back()->withErrors(['evidence' => 'Evidence is required before this obligation can be completed.']);
        }

        DB::transaction(function () use ($request, $obligation, $validated, $recurrence): void {
            if ($request->hasFile('evidence')) {
                $obligation->evidence()->create([
                    'uploaded_by_user_id' => $request->user()->getKey(),
                    'label' => $request->file('evidence')->getClientOriginalName(),
                    'path' => $request->file('evidence')->store('compliance-evidence', 'private'),
                    'notes' => $validated['notes'] ?? null,
                ]);
            }

            $obligation->update([
                'status' => 'completed',
                'completed_at' => now()->toDateString(),
                'completed_by_user_id' => $request->user()->getKey(),
                'notes' => $validated['notes'] ?? $obligation->notes,
            ]);
            $this->log($obligation, 'completed', $request->user());
            $recurrence->createNextOccurrence($obligation->fresh('checklistItems'));
        });

        return back()->with('success', 'Obligation completed and the next occurrence was generated where applicable.');
    }

    public function waive(Request $request, OrganisationComplianceObligation $obligation): RedirectResponse
    {
        abort_unless($obligation->isVisibleTo($request->user()), 403);
        abort_unless($request->user()->canManageComplianceWorkspace(), 403);

        $validated = $request->validate(['waiver_reason' => ['required', 'string', 'max:3000']]);
        $obligation->update(['status' => 'waived', 'waiver_reason' => $validated['waiver_reason'], 'waived_at' => now()->toDateString()]);
        $this->log($obligation, 'waived', $request->user(), $validated);

        return back()->with('success', 'Occurrence waived with an audit trail.');
    }

    public function checklist(Request $request, OrganisationComplianceObligation $obligation): RedirectResponse
    {
        abort_unless($obligation->isVisibleTo($request->user()), 403);

        $completed = collect($request->input('checklist', []))->map(fn (string $id): int => (int) $id);

        foreach ($obligation->checklistItems as $item) {
            $isCompleted = $completed->contains($item->getKey());
            $item->update([
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
                'completed_by_user_id' => $isCompleted ? $request->user()->getKey() : null,
            ]);
        }

        $this->log($obligation, 'checklist_updated', $request->user());

        return back()->with('success', 'Checklist progress saved.');
    }

    public function export(Request $request)
    {
        $rows = $this->filteredObligations($request)->with('assignedUser')->orderBy('due_at')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Title', 'Category', 'Regulator', 'Risk', 'Status', 'Due date', 'Owner']);
            foreach ($rows as $row) {
                fputcsv($handle, [$row->title, $row->category, $row->regulator, $row->risk_level, $row->status, $row->due_at?->toDateString(), $row->assignedUser?->name]);
            }
            fclose($handle);
        }, 'compliance-schedule.csv', ['Content-Type' => 'text/csv']);
    }

    private function filteredObligations(Request $request)
    {
        return OrganisationComplianceObligation::query()
            ->when(! $request->user()->canManageComplianceWorkspace(), fn ($query) => $query
                ->where(fn ($query) => $query
                    ->where('assigned_user_id', $request->user()->getKey())
                    ->orWhere('reviewer_user_id', $request->user()->getKey())))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('risk_level'), fn ($query) => $query->where('risk_level', $request->string('risk_level')))
            ->when($request->filled('owner'), fn ($query) => $query->where('assigned_user_id', $request->integer('owner')))
            ->when($request->filled('regulator'), fn ($query) => $query->where('regulator', 'like', '%'.$request->string('regulator').'%'))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('due_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('due_at', '<=', $request->date('to')));
    }

    private function usersFor(User $user)
    {
        if ($user->isAdmin()) {
            return User::orderBy('name')->get();
        }

        $organization = $user->activeOrganization();

        return $organization?->members()->orderBy('name')->get() ?? collect([$user]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function authorizeAssignedUsers(Request $request, array $validated): void
    {
        $organization = $request->user()->activeOrganization();
        $memberIds = $organization?->members()->pluck('users.id')->all() ?? [];

        foreach (['assigned_user_id', 'reviewer_user_id'] as $key) {
            if (isset($validated[$key]) && ! in_array((int) $validated[$key], $memberIds, true)) {
                abort(403);
            }
        }
    }

    private function log(OrganisationComplianceObligation $obligation, string $action, ?User $user, array $properties = []): void
    {
        $obligation->activityLogs()->create([
            'user_id' => $user?->getKey(),
            'action' => $action,
            'properties' => $properties,
        ]);
    }
}
