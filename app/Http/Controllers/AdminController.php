<?php

namespace App\Http\Controllers;

use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\MonthlyPayment;
use App\Models\Organization;
use App\Models\RopaRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), Response::HTTP_FORBIDDEN);

        return view('admin.dashboard', [
            'failedPayments' => MonthlyPayment::with(['organization', 'user'])
                ->where('status', 'failed')
                ->latest()
                ->limit(25)
                ->get(),
            'payments' => MonthlyPayment::with(['organization', 'user'])
                ->latest()
                ->limit(25)
                ->get(),
            'users' => User::with('organization')->latest()->limit(25)->get(),
            'organizations' => Organization::withCount(['users', 'dp1s'])->latest()->limit(25)->get(),
            'dp1Reports' => FormDp1::withoutGlobalScope('organization')->with('organization')->latest()->limit(15)->get(),
            'dp2Reports' => FormDp2::withoutGlobalScope('organization')->with('organization')->latest()->limit(15)->get(),
            'ropaReports' => RopaRecord::withoutGlobalScope('organization')->with('organization')->latest()->limit(15)->get(),
            'incidentReports' => BreachIncident::withoutGlobalScope('organization')->with('organization')->latest('detected_at')->limit(15)->get(),
            'totals' => [
                'users' => User::count(),
                'organizations' => Organization::count(),
                'failed_payments' => MonthlyPayment::where('status', 'failed')->count(),
                'reports' => FormDp1::withoutGlobalScope('organization')->count()
                    + FormDp2::withoutGlobalScope('organization')->count()
                    + RopaRecord::withoutGlobalScope('organization')->count()
                    + BreachIncident::withoutGlobalScope('organization')->count(),
            ],
        ]);
    }
}
