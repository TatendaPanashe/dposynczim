@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Organisation evidence" title="Compliance forms" description="Track consent forms, cross-border authorisations, and general compliance evidence for the selected organisation.">
    <x-slot:actions><a href="{{ route('compliance.forms.create') }}" class="button-primary">Add form <span aria-hidden="true">+</span></a></x-slot:actions>
</x-page-heading>

<section class="grid gap-4 sm:grid-cols-3">
    @foreach($types as $type => $label)
        @php($count = $forms->where('type', $type)->count())
        <div class="surface p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-6 text-3xl font-bold">{{ $count }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $count === 1 ? 'record' : 'records' }} in this workspace</p>
        </div>
    @endforeach
</section>

<section class="surface mt-8 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-[1100px] w-full table-fixed text-left">
            <colgroup>
                <col class="w-[22%]">
                <col class="w-[18%]">
                <col class="w-[13%]">
                <col class="w-[14%]">
                <col class="w-[14%]">
                <col class="w-[13%]">
                <col class="w-[6%]">
            </colgroup>
            <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Form</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Owner</th>
                    <th class="px-5 py-3">Review</th>
                    <th class="px-5 py-3">Expiry</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                    <tr class="border-b border-slate-100 last:border-0">
                        <td class="px-5 py-5 align-top">
                            <p class="text-sm font-semibold text-slate-900">{{ $form->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $form->reference ?: 'No reference' }}</p>
                        </td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ $form->typeLabel() }}</td>
                        <td class="px-5 py-5 align-top"><x-status :label="$form->statusLabel()" :tone="$form->statusTone()" /></td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ $form->owner ?: 'Unassigned' }}</td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ $form->review_due_at?->format('d M Y') ?? 'Not set' }}</td>
                        <td class="px-5 py-5 align-top text-sm text-slate-600">{{ $form->expires_at?->format('d M Y') ?? 'Not set' }}</td>
                        <td class="px-5 py-5 align-top text-right"><a href="{{ route('compliance.forms.edit', ['form' => $form]) }}" class="text-xs font-bold text-cyan-700 hover:text-cyan-900">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center"><p class="text-lg font-bold">No compliance forms yet</p><p class="mt-2 text-sm text-slate-500">Add consent records, transfer authorisations, or general evidence for this organisation.</p><a href="{{ route('compliance.forms.create') }}" class="button-primary mt-6">Add first form</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
