@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DP2 · Appointment register" title="Your data protection officer" description="Keep the appointed DPO, reporting line, and evidence of professional readiness current.">
    <x-slot:actions><a href="{{ route('compliance.dp2.create') }}" class="button-primary">Add appointment <span aria-hidden="true">+</span></a></x-slot:actions>
</x-page-heading>
<div class="surface overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-[760px] w-full table-fixed text-left">
            <colgroup><col class="w-[25%]"><col class="w-[25%]"><col class="w-[20%]"><col class="w-[17%]"><col class="w-[13%]"></colgroup>
            <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">DPO</th><th class="px-5 py-3">Reporting line</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Form</th></tr></thead>
            <tbody>
                @forelse($appointments as $appointment)
                    <tr class="border-b border-slate-100 last:border-0"><td class="px-5 py-5 align-middle"><p class="font-semibold">{{ $appointment->full_name }}</p><p class="mt-1 text-xs text-slate-500">Appointed {{ $appointment->appointed_at->format('d M Y') }}</p></td><td class="px-5 py-5 text-sm text-slate-600">{{ $appointment->reporting_line }}</td><td class="px-5 py-5 text-sm text-slate-600"><p>{{ $appointment->official_email }}</p><p class="mt-1 text-xs">{{ $appointment->official_phone }}</p></td><td class="px-5 py-5"><x-status label="Active" tone="green" /></td><td class="px-5 py-5"><a href="{{ route('compliance.dp2.download', $appointment) }}" class="text-xs font-bold text-cyan-800 hover:underline">Download</a></td></tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-16 text-center"><p class="text-lg font-bold">No DPO appointment recorded</p><p class="mt-2 text-sm text-slate-500">A named DPO is a core part of your DP2 submission.</p><a href="{{ route('compliance.dp2.create') }}" class="button-primary mt-6">Record the appointment</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection