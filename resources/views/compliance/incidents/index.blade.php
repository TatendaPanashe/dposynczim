@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DP3 · Incident register" title="Breach and incident response" description="Log a personal data breach as soon as it is detected. Every incident starts a 24-hour POTRAZ notification clock.">
    <x-slot:actions><a href="{{ route('compliance.incidents.create') }}" class="button-primary">Log incident <span aria-hidden="true">+</span></a></x-slot:actions>
</x-page-heading>
<div class="mb-6 border border-amber-200 bg-amber-50 px-5 py-4"><p class="text-sm font-bold text-amber-900">24-hour response window</p><p class="mt-1 text-sm text-amber-800">The DP3 notice deadline is calculated from the detected-at time and cannot be extended by editing the form.</p></div>
<div class="surface overflow-hidden">
    <div class="hidden grid-cols-[0.8fr_1.4fr_0.7fr_0.8fr_0.8fr] gap-4 border-b border-slate-200 bg-slate-50 px-5 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-500 md:grid"><span>Reference</span><span>Incident</span><span>Severity</span><span>Detected</span><span>Deadline</span></div>
    @forelse($incidents as $incident)
        <div class="grid gap-3 border-b border-slate-100 px-5 py-5 last:border-0 md:grid-cols-[0.8fr_1.4fr_0.7fr_0.8fr_0.8fr_0.5fr] md:items-center md:gap-4"><p class="font-mono text-xs text-slate-500">{{ $incident->reference }}</p><div><p class="font-semibold">{{ $incident->title }}</p><p class="mt-1 text-xs text-slate-500">{{ ucfirst($incident->status) }}</p></div><div><x-status :label="$incident->severity" :tone="in_array($incident->severity, ['high', 'critical']) ? 'red' : 'amber'" /></div><p class="text-sm text-slate-600">{{ $incident->detected_at->format('d M Y, H:i') }}</p><p class="text-sm font-semibold {{ $incident->sla_due_at->isPast() ? 'text-rose-700' : 'text-slate-700' }}">{{ $incident->sla_due_at->format('d M, H:i') }}</p><a href="{{ route('compliance.incidents.download', $incident) }}" class="text-xs font-bold text-cyan-800 hover:underline">Download</a></div>
    @empty
        <div class="px-5 py-16 text-center"><p class="text-lg font-bold">No incidents logged</p><p class="mt-2 text-sm text-slate-500">When an incident occurs, this is the first place to record it.</p><a href="{{ route('compliance.incidents.create') }}" class="button-primary mt-6">Log an incident</a></div>
    @endforelse
</div>
@endsection