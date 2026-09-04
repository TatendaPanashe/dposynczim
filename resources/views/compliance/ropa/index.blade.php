@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="ROPA · Processing register" title="Record of processing activities" description="Maintain the operational record behind your DP1. Sensitive fields are encrypted at rest for your organisation.">
    <x-slot:actions><div class="flex flex-wrap gap-3"><a href="{{ route('compliance.ropa.create') }}" class="button-secondary">Add activity <span aria-hidden="true">+</span></a><a href="{{ route('compliance.ropa.download') }}" class="button-primary">Export full register (.xls) <span aria-hidden="true">↓</span></a></div></x-slot:actions>
</x-page-heading>
<div class="surface overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-[760px] w-full table-fixed text-left">
            <colgroup><col class="w-[28%]"><col class="w-[32%]"><col class="w-[22%]"><col class="w-[18%]"></colgroup>
            <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Processing activity</th><th class="px-5 py-3">Purpose</th><th class="px-5 py-3">Legal basis</th><th class="px-5 py-3">Owner</th></tr></thead>
            <tbody>
                @forelse($records as $record)
                    <tr class="border-b border-slate-100 last:border-0"><td class="px-5 py-5 align-middle"><p class="font-semibold">{{ $record->processing_activity }}</p><p class="mt-1 text-xs text-slate-500">Retention: {{ $record->retention_period }}</p></td><td class="px-5 py-5 text-sm leading-5 text-slate-600">{{ $record->purpose }}</td><td class="px-5 py-5 text-sm text-slate-600">{{ $record->legal_basis }}</td><td class="px-5 py-5 text-sm text-slate-600">{{ $record->owner }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-16 text-center"><p class="text-lg font-bold">Your register is empty</p><p class="mt-2 text-sm text-slate-500">Start with the processing activity that carries the most personal data.</p><a href="{{ route('compliance.ropa.create') }}" class="button-primary mt-6">Add first activity</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection