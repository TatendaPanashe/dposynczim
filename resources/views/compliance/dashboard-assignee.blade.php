@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Assigned workspace" title="My compliance work" description="{{ $activeOrganization?->name ?? 'Your workspace' }} · tasks assigned to you or waiting for your review.">
    <x-slot:actions><a href="{{ route('compliance.calendar.index') }}" class="button-secondary">Open calendar</a></x-slot:actions>
</x-page-heading>

<section class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Due today</span><p class="mt-4 text-3xl font-bold">{{ $dueToday }}</p><p class="text-xs text-slate-500">Includes overdue assigned work</p></div>
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Due in 7 days</span><p class="mt-4 text-3xl font-bold">{{ $due7 }}</p><p class="text-xs text-slate-500">Upcoming assignments</p></div>
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Overdue</span><p class="mt-4 text-3xl font-bold text-rose-700">{{ $overdue }}</p><p class="text-xs text-slate-500">Needs attention</p></div>
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Completed this month</span><p class="mt-4 text-3xl font-bold">{{ $completedMonth }}</p><p class="text-xs text-slate-500">Your closed work</p></div>
</section>

<section class="surface overflow-hidden">
    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="font-bold">Open assigned tasks</h2>
        <p class="mt-1 text-xs text-slate-500">Open a task to update checklist progress, upload evidence, or mark it complete.</p>
    </div>
    <div class="divide-y divide-slate-100">
        @forelse($openTasks as $obligation)
            <a href="{{ route('compliance.calendar.show', $obligation) }}" class="grid gap-3 px-5 py-4 transition hover:bg-blue-50 md:grid-cols-[1fr_auto] md:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold">{{ $obligation->title }}</p>
                        <x-status :label="str($obligation->status)->headline()->toString()" :tone="$obligation->due_at->isPast() ? 'red' : 'cyan'" />
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ $obligation->category }} · {{ $obligation->reviewer_user_id === auth()->id() ? 'Review' : 'Assigned' }}</p>
                </div>
                <span class="text-sm font-bold">{{ $obligation->due_at->format('d M Y') }}</span>
            </a>
        @empty
            <div class="px-5 py-16 text-center text-sm text-slate-500">No open tasks are assigned to you.</div>
        @endforelse
    </div>
</section>
@endsection
