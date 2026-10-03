<?php

namespace App\Http\Controllers;

use App\Models\OrganisationComplianceObligation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyComplianceTaskController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('compliance.tasks.index', [
            'obligations' => OrganisationComplianceObligation::with(['assignedUser', 'reviewer'])
                ->where(fn ($query) => $query
                    ->where('assigned_user_id', $request->user()->getKey())
                    ->orWhere('reviewer_user_id', $request->user()->getKey()))
                ->whereNotIn('status', ['completed', 'waived'])
                ->orderBy('due_at')
                ->paginate(20),
        ]);
    }
}
