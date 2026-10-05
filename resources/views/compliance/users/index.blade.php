@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Administration · Users" title="Workspace users" description="Create compliance users for {{ $organization?->name ?? 'the active workspace' }} and assign calendar tasks to them.">
    <x-slot:actions><a href="{{ route('compliance.calendar.index') }}" class="button-secondary">Assign tasks</a></x-slot:actions>
</x-page-heading>

<section class="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
    <form method="POST" action="{{ route('compliance.users.store') }}" class="surface grid gap-5 p-6">
        @csrf
        <div>
            <h2 class="text-xl font-bold">Create user</h2>
            <p class="mt-1 text-sm text-slate-500">They can log in immediately with this email and password.</p>
        </div>
        <x-field name="name" label="Full name" required />
        <x-field name="email" label="Email" type="email" required />
        <x-field name="password" label="Temporary password" type="password" required />
        <x-field name="password_confirmation" label="Confirm password" type="password" required />
        <div>
            <label for="role" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Workspace role</label>
            <select id="role" name="role" class="form-control" required>
                <option value="task_user" @selected(old('role', 'task_user') === 'task_user')>Task user</option>
                <option value="compliance_officer" @selected(old('role') === 'compliance_officer')>Compliance officer</option>
            </select>
            @error('role')<span class="mt-1 block text-xs text-rose-700">{{ $message }}</span>@enderror
        </div>
        <button class="button-primary">Create user</button>
    </form>

    <div class="surface overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-bold">Current workspace users</h2>
            <p class="mt-1 text-xs text-slate-500">Task users see only their assigned work and the calendar. Compliance officers can manage compliance workflows.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($users as $user)
                <div class="grid gap-3 px-5 py-4 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <p class="font-semibold">{{ $user->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                    </div>
                    <x-status :label="str($user->pivot->role)->headline()->toString()" :tone="$user->pivot->role === 'task_user' ? 'cyan' : 'green'" />
                </div>
            @empty
                <div class="px-5 py-12 text-center text-sm text-slate-500">No users are attached to this workspace yet.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
