<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DpoProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('compliance.dpo-profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'dpo_registration_number' => ['nullable', 'string', 'max:255'],
            'dpo_address' => ['required', 'string', 'max:2000'],
            'dpo_qualifications' => ['required', 'string', 'max:3000'],
            'dpo_certification_status' => ['required', 'string', 'max:255'],
            'dpo_reporting_line' => ['required', 'string', 'max:255'],
            'dpo_official_phone' => ['required', 'string', 'max:50'],
            'dpo_mobile' => ['required', 'string', 'max:50'],
        ]);

        $request->user()->update([
            ...$validated,
            'dpo_qualifications' => $this->lines($validated['dpo_qualifications']),
        ]);

        return to_route('compliance.organizations.index')->with('success', 'Your DPO profile is ready. Add or choose an organisation to continue.');
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
