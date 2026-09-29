@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Today · {{ now()->format('d M Y') }}" title="Your compliance workspace" description="A clear view of your organisation's regulatory readiness, open actions, and evidence trail.">
    <x-slot:actions><a href="{{ route('compliance.dp1.create') }}" class="button-primary">Start DP1 application <span aria-hidden="true">↗</span></a></x-slot:actions>
</x-page-heading>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="surface p-5"><div class="flex items-start justify-between"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">DP1 drafts</span><span class="text-cyan-700">▣</span></div><p class="mt-6 text-3xl font-bold tracking-tight">{{ $dp1Drafts }}</p><p class="mt-1 text-xs text-slate-500">Applications in progress</p></div>
    <div class="surface p-5"><div class="flex items-start justify-between"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Open incidents</span><span class="text-rose-700">!</span></div><p class="mt-6 text-3xl font-bold tracking-tight">{{ $activeIncidents }}</p><p class="mt-1 text-xs text-slate-500">24-hour DP3 clock monitored</p></div>
    <div class="surface p-5"><div class="flex items-start justify-between"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">ROPA coverage</span><span class="text-cyan-700">≡</span></div><p class="mt-6 text-3xl font-bold tracking-tight">{{ $ropaRecords }}</p><p class="mt-1 text-xs text-slate-500">Processing activities recorded</p></div>
    <div class="surface p-5"><div class="flex items-start justify-between"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">DPO appointment</span><span class="text-emerald-700">◎</span></div><p class="mt-6 text-3xl font-bold tracking-tight">{{ $dpoStatus ? 'Active' : 'Missing' }}</p><p class="mt-1 text-xs text-slate-500">DP2 appointment status</p></div>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
    <div class="surface">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-bold">Organisation compliance status</h2>
            <p class="mt-1 text-xs text-slate-500">{{ $activeOrganization?->name ?? 'No organisation selected' }} · Required evidence and operational readiness.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($complianceChecklist as $item)
                <a href="{{ $item['href'] }}" class="grid gap-3 px-5 py-4 transition hover:bg-slate-50 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                            <x-status :label="$item['complete'] ? 'Ready' : 'Action needed'" :tone="$item['complete'] ? 'green' : 'amber'" />
                            @isset($item['status'])
                                <span class="text-xs font-semibold text-slate-500">{{ $item['status'] }}</span>
                            @endisset
                        </div>
                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $item['description'] }}</p>
                    </div>
                    <span class="text-xs font-bold text-cyan-700">Open ↗</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="surface p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Compliance form statuses</p>
        <div class="mt-5 grid gap-3">
            @foreach([
                'active' => ['Active', 'green'],
                'approved' => ['Approved', 'green'],
                'submitted' => ['Submitted', 'cyan'],
                'draft' => ['Draft', 'amber'],
                'needs_review' => ['Needs review', 'amber'],
                'expired' => ['Expired', 'red'],
            ] as $status => [$label, $tone])
                <div class="flex items-center justify-between border border-slate-200 px-4 py-3">
                    <x-status :label="$label" :tone="$tone" />
                    <span class="text-lg font-bold">{{ $formStatusCounts->get($status, 0) }}</span>
                </div>
            @endforeach
        </div>
        <a href="{{ route('compliance.forms.index') }}" class="button-secondary mt-5 w-full">Manage forms</a>
    </div>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
    <div class="surface">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><div><h2 class="font-bold">Incident watch</h2><p class="mt-1 text-xs text-slate-500">The latest events needing a response.</p></div><a href="{{ route('compliance.incidents.index') }}" class="text-xs font-bold text-cyan-700 hover:text-cyan-900">View all ↗</a></div>
        <div class="divide-y divide-slate-100">
            @forelse($recentIncidents as $incident)
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"><div><div class="flex items-center gap-3"><span class="font-mono text-xs text-slate-400">{{ $incident->reference }}</span><x-status :label="$incident->severity" :tone="$incident->severity === 'critical' ? 'red' : 'amber'" /></div><p class="mt-2 text-sm font-semibold">{{ $incident->title }}</p></div><div class="text-left sm:text-right"><p class="text-xs font-semibold text-slate-700">Due {{ $incident->sla_due_at->format('d M, H:i') }}</p><p class="mt-1 text-xs text-slate-500">{{ $incident->status }}</p></div></div>
            @empty
                <div class="px-5 py-12 text-center"><p class="font-semibold">No incidents logged</p><p class="mt-1 text-sm text-slate-500">Your incident register is clear.</p></div>
            @endforelse
        </div>
    </div>
    <div class="surface p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Annual renewal</p>
        @if($renewal)
            <h2 class="mt-3 text-2xl font-bold">{{ $renewal->renewal_due_at->diffInDays(now()) }} days</h2><p class="mt-1 text-sm text-slate-500">until your next DP1 renewal on {{ $renewal->renewal_due_at->format('d M Y') }}.</p>
            <a href="{{ route('compliance.dp1.create') }}" class="button-secondary mt-6 w-full">Review DP1 details</a>
        @else
            <h2 class="mt-3 text-xl font-bold">No renewal date yet</h2><p class="mt-1 text-sm leading-6 text-slate-500">Save your first DP1 draft to begin tracking the annual renewal cycle.</p>
            <a href="{{ route('compliance.dp1.create') }}" class="button-secondary mt-6 w-full">Create DP1 draft</a>
        @endif
    </div>
</section>
@endsection
