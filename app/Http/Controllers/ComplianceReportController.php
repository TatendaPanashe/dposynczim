<?php

namespace App\Http\Controllers;

use App\Models\OrganisationComplianceObligation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplianceReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $from = $request->date('from')?->toDateString() ?? now()->startOfMonth()->toDateString();
        $to = $request->date('to')?->toDateString() ?? now()->endOfMonth()->toDateString();
        $due = OrganisationComplianceObligation::query()
            ->whereBetween('due_at', [$from, $to])
            ->where('status', '!=', 'waived');
        $dueCount = (clone $due)->count();
        $completedOnTime = (clone $due)->where('status', 'completed')->whereColumn('completed_at', '<=', 'due_at')->count();

        return view('compliance.reports.index', [
            'from' => $from,
            'to' => $to,
            'score' => $dueCount > 0 ? round(($completedOnTime / $dueCount) * 100) : 100,
            'dueCount' => $dueCount,
            'completedOnTime' => $completedOnTime,
            'byCategory' => OrganisationComplianceObligation::query()
                ->selectRaw('category, status, count(*) as total')
                ->whereBetween('due_at', [$from, $to])
                ->groupBy('category', 'status')
                ->orderBy('category')
                ->get(),
            'overdue' => OrganisationComplianceObligation::with('assignedUser')
                ->where(fn ($query) => $query->where('status', 'overdue')->orWhere(fn ($inner) => $inner->whereDate('due_at', '<', today())->whereNotIn('status', ['completed', 'waived'])))
                ->orderBy('due_at')
                ->get(),
            'upcoming' => OrganisationComplianceObligation::with('assignedUser')->whereBetween('due_at', [today(), today()->addDays(30)])->orderBy('due_at')->get(),
            'ownerPerformance' => OrganisationComplianceObligation::with('assignedUser')
                ->whereBetween('due_at', [$from, $to])
                ->get()
                ->groupBy(fn (OrganisationComplianceObligation $obligation): string => $obligation->assignedUser?->name ?? 'Unassigned'),
        ]);
    }
}
