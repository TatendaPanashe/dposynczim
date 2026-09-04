<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PrivacyPolicyController extends Controller
{
    public function create(Request $request): View
    {
        return view('compliance.privacy-policies.create', [
            'organization' => $request->user()->activeOrganization(),
        ]);
    }

    public function download(Request $request): Response
    {
        $validated = $request->validate([
            'policy_type' => ['required', 'in:employee,customer,website'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_address' => ['required', 'string', 'max:2000'],
            'effective_date' => ['required', 'date'],
            'processing_summary' => ['required', 'string', 'max:5000'],
            'retention_summary' => ['required', 'string', 'max:3000'],
        ]);

        $organization = $request->user()->activeOrganization();

        return response(view('exports.privacy-policy', [
            'organization' => $organization,
            'policy' => $validated,
        ])->render(), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.strtolower($validated['policy_type']).'-privacy-policy.html"',
        ]);
    }
}
