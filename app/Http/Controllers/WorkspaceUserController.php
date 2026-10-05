<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkspaceUserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        $organization = $request->user()->activeOrganization();

        return view('compliance.users.index', [
            'organization' => $organization,
            'users' => $organization?->members()->orderBy('name')->get() ?? collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManager($request);

        $organization = $request->user()->activeOrganization();
        abort_if($organization === null, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
            'role' => ['required', Rule::in(['compliance_officer', 'task_user'])],
        ]);

        DB::transaction(function () use ($organization, $validated): void {
            $user = User::create([
                'organization_id' => $organization->getKey(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $organization->members()->attach($user->getKey(), [
                'role' => $validated['role'],
            ]);
        });

        return back()->with('success', 'Workspace user created and can now be assigned compliance tasks.');
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless($request->user()->canManageComplianceWorkspace(), 403);
    }
}
