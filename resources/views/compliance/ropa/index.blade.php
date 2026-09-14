@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="ROPA · Processing register" title="Record of processing activities" description="Maintain the operational record behind your DP1. The export uses the DPA ROPA register template.">
    <x-slot:actions><div class="flex flex-wrap gap-3"><a href="{{ route('compliance.ropa.create') }}" class="button-secondary">Add activity <span aria-hidden="true">+</span></a><a href="{{ route('compliance.ropa.download') }}" class="button-primary">Export full register (.xlsx) <span aria-hidden="true">↓</span></a></div></x-slot:actions>
</x-page-heading>
<div class="surface overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-[1320px] w-full table-fixed text-left">
            <colgroup>
                <col class="w-[16%]">
                <col class="w-[18%]">
                <col class="w-[18%]">
                <col class="w-[13%]">
                <col class="w-[13%]">
                <col class="w-[13%]">
                <col class="w-[9%]">
            </colgroup>
            <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Business function</th>
                    <th class="px-5 py-3">What is it?</th>
                    <th class="px-5 py-3">Purpose</th>
                    <th class="px-5 py-3">Lawful basis</th>
                    <th class="px-5 py-3">Data subjects</th>
                    <th class="px-5 py-3">Retention</th>
                    <th class="px-5 py-3">Risk impact</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr class="border-b border-slate-100 last:border-0">
                        <td class="px-5 py-5 align-top text-sm text-slate-600">
                            <p class="font-semibold text-slate-900">{{ $record->business_function ?: $record->owner }}</p>
                            <p class="mt-1 text-xs text-slate-500">Owner: {{ $record->owner }}</p>
                        </td>
                        <td class="px-5 py-5 align-top text-sm leading-5 text-slate-600">{{ $record->processing_activity }}</td>
                        <td class="px-5 py-5 align-top text-sm leading-5 text-slate-600">{{ $record->purpose }}</td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ $record->legal_basis }}</td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ implode('; ', $record->data_subject_categories ?? []) }}</td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">
                            <p>{{ $record->retention_period }}</p>
                            @if($record->retention_basis)<p class="mt-1 text-xs text-slate-500">{{ $record->retention_basis }}</p>@endif
                        </td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ $record->risk_impact ?: 'Not assessed' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center"><p class="text-lg font-bold">Your register is empty</p><p class="mt-2 text-sm text-slate-500">Start with the processing activity that carries the most personal data.</p><a href="{{ route('compliance.ropa.create') }}" class="button-primary mt-6">Add first activity</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
