<?php

namespace App\Http\Controllers;

use App\Models\BreachIncident;
use App\Models\ComplianceForm;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\OrganisationComplianceObligation;
use App\Models\RopaRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user?->needsDpoProfileSetup()) {
            return to_route('compliance.dpo-profile.edit');
        }

        if ($user?->isTaskUser()) {
            $assignedQuery = OrganisationComplianceObligation::with(['assignedUser', 'reviewer'])
                ->where(fn ($query) => $query
                    ->where('assigned_user_id', $user->getKey())
                    ->orWhere('reviewer_user_id', $user->getKey()));

            return view('compliance.dashboard-assignee', [
                'activeOrganization' => $user->activeOrganization(),
                'openTasks' => (clone $assignedQuery)->whereNotIn('status', ['completed', 'waived'])->orderBy('due_at')->limit(8)->get(),
                'dueToday' => (clone $assignedQuery)->whereDate('due_at', '<=', today())->whereNotIn('status', ['completed', 'waived'])->count(),
                'due7' => (clone $assignedQuery)->whereBetween('due_at', [today(), today()->addDays(7)])->whereNotIn('status', ['completed', 'waived'])->count(),
                'overdue' => (clone $assignedQuery)->whereDate('due_at', '<', today())->whereNotIn('status', ['completed', 'waived'])->count(),
                'completedMonth' => (clone $assignedQuery)->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            ]);
        }

        $activeOrganization = $user?->activeOrganization();
        $latestDp1 = FormDp1::latest()->first();
        $activeDp2 = FormDp2::where('status', 'active')->latest()->first();
        $ropaRecords = RopaRecord::count();
        $consentForms = ComplianceForm::where('type', 'consent')->get();
        $crossBorderForms = ComplianceForm::where('type', 'cross_border_authorisation')->get();
        $generalForms = ComplianceForm::where('type', 'general')->get();
        $activeIncidents = BreachIncident::whereIn('status', ['open', 'investigating'])->count();
        $periodDue = OrganisationComplianceObligation::whereBetween('due_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->where('status', '!=', 'waived');
        $periodDueCount = (clone $periodDue)->count();
        $completedOnTime = (clone $periodDue)->where('status', 'completed')->whereColumn('completed_at', '<=', 'due_at')->count();

        return view('compliance.dashboard', [
            'activeOrganization' => auth()->user()?->activeOrganization(),
            'dp1Drafts' => FormDp1::where('status', 'draft')->count(),
            'activeIncidents' => $activeIncidents,
            'calendarStats' => [
                'active' => OrganisationComplianceObligation::whereNotIn('status', ['completed', 'waived'])->count(),
                'due7' => OrganisationComplianceObligation::whereBetween('due_at', [today(), today()->addDays(7)])->whereNotIn('status', ['completed', 'waived'])->count(),
                'due30' => OrganisationComplianceObligation::whereBetween('due_at', [today(), today()->addDays(30)])->whereNotIn('status', ['completed', 'waived'])->count(),
                'due90' => OrganisationComplianceObligation::whereBetween('due_at', [today(), today()->addDays(90)])->whereNotIn('status', ['completed', 'waived'])->count(),
                'overdue' => OrganisationComplianceObligation::whereDate('due_at', '<', today())->whereNotIn('status', ['completed', 'waived'])->count(),
                'completedMonth' => OrganisationComplianceObligation::whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'score' => $periodDueCount > 0 ? round(($completedOnTime / $periodDueCount) * 100) : 100,
            ],
            'todayActions' => OrganisationComplianceObligation::with('assignedUser')->whereDate('due_at', '<=', today())->whereNotIn('status', ['completed', 'waived'])->orderBy('due_at')->limit(6)->get(),
            'highRiskUpcoming' => OrganisationComplianceObligation::with('assignedUser')->whereIn('risk_level', ['high', 'critical'])->whereDate('due_at', '>=', today())->whereNotIn('status', ['completed', 'waived'])->orderBy('due_at')->limit(6)->get(),
            'calendarByCategory' => OrganisationComplianceObligation::selectRaw('category, count(*) as total')->whereNotIn('status', ['completed', 'waived'])->groupBy('category')->orderByDesc('total')->get(),
            'teamWorkload' => OrganisationComplianceObligation::with('assignedUser')->whereNotIn('status', ['completed', 'waived'])->get()->groupBy(fn (OrganisationComplianceObligation $obligation): string => $obligation->assignedUser?->name ?? 'Unassigned'),
            'ropaRecords' => $ropaRecords,
            'dpoStatus' => $activeDp2 !== null,
            'recentIncidents' => BreachIncident::latest('detected_at')->limit(4)->get(),
            'renewal' => FormDp1::whereNotNull('renewal_due_at')->latest('renewal_due_at')->first(),
            'formStatusCounts' => ComplianceForm::get()
                ->groupBy(fn (ComplianceForm $form): string => $form->computedStatus())
                ->map->count(),
            'complianceChecklist' => [
                [
                    'label' => 'Organisation profile',
                    'description' => 'Core organisation details are captured for filings and evidence.',
                    'complete' => $activeOrganization !== null && filled($activeOrganization->name) && filled($activeOrganization->business_sector) && filled($activeOrganization->physical_address),
                    'href' => route('compliance.organizations.index'),
                ],
                [
                    'label' => 'DPO profile',
                    'description' => 'Officer contact, qualifications, certification, and reporting line are ready.',
                    'complete' => auth()->user()?->hasCompletedDpoProfile() ?? false,
                    'href' => route('compliance.dpo-profile.edit'),
                ],
                [
                    'label' => 'DP1 registration',
                    'description' => 'Data controller registration workflow is drafted or submitted.',
                    'complete' => $latestDp1 !== null,
                    'status' => $latestDp1?->status,
                    'href' => route('compliance.dp1.create'),
                ],
                [
                    'label' => 'DP2 appointment',
                    'description' => 'A DPO appointment record exists for this organisation.',
                    'complete' => $activeDp2 !== null,
                    'status' => $activeDp2?->status,
                    'href' => route('compliance.dp2.index'),
                ],
                [
                    'label' => 'ROPA register',
                    'description' => 'Processing activities are recorded and exportable.',
                    'complete' => $ropaRecords > 0,
                    'status' => $ropaRecords.' recorded',
                    'href' => route('compliance.ropa.index'),
                ],
                [
                    'label' => 'Consent forms',
                    'description' => 'Consent evidence exists for processing that relies on consent.',
                    'complete' => $consentForms->contains(fn (ComplianceForm $form): bool => in_array($form->computedStatus(), ['active', 'approved'], true)),
                    'status' => $consentForms->count().' recorded',
                    'href' => route('compliance.forms.index'),
                ],
                [
                    'label' => 'Cross-border authorisation',
                    'description' => 'Transfers outside Zimbabwe have authorisation and safeguards recorded.',
                    'complete' => $crossBorderForms->contains(fn (ComplianceForm $form): bool => in_array($form->computedStatus(), ['active', 'approved', 'submitted'], true)),
                    'status' => $crossBorderForms->count().' recorded',
                    'href' => route('compliance.forms.index'),
                ],
                [
                    'label' => 'General compliance forms',
                    'description' => 'Policies, assessments, approvals, and other evidence are tracked.',
                    'complete' => $generalForms->contains(fn (ComplianceForm $form): bool => in_array($form->computedStatus(), ['active', 'approved'], true)),
                    'status' => $generalForms->count().' recorded',
                    'href' => route('compliance.forms.index'),
                ],
                [
                    'label' => 'Incident readiness',
                    'description' => 'Open breach records and DP3 response work are visible.',
                    'complete' => $activeIncidents === 0,
                    'status' => $activeIncidents.' open',
                    'href' => route('compliance.incidents.index'),
                ],
            ],
        ]);
    }
}
