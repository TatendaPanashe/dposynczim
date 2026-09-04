<?php

namespace App\Http\Controllers;

use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\RopaRecord;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('compliance.dashboard', [
            'activeOrganization' => auth()->user()?->activeOrganization(),
            'dp1Drafts' => FormDp1::where('status', 'draft')->count(),
            'activeIncidents' => BreachIncident::whereIn('status', ['open', 'investigating'])->count(),
            'ropaRecords' => RopaRecord::count(),
            'dpoStatus' => FormDp2::where('status', 'active')->exists(),
            'recentIncidents' => BreachIncident::latest('detected_at')->limit(4)->get(),
            'renewal' => FormDp1::whereNotNull('renewal_due_at')->latest('renewal_due_at')->first(),
        ]);
    }
}
