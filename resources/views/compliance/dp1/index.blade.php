@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DP1 · Application register" title="Data controller applications" description="Review saved DP1 applications and download the official POTRAZ form only when you are ready.">
    <x-slot:actions><a href="{{ route('compliance.dp1.create') }}" class="button-primary">New DP1 application <span aria-hidden="true">+</span></a></x-slot:actions>
</x-page-heading>
<div class="surface overflow-hidden">
    <div class="overflow-x-auto">
        <div class="hidden min-w-[760px] grid-cols-[1fr_0.7fr_0.7fr_0.8fr_0.5fr] gap-4 border-b border-slate-200 bg-slate-50 px-5 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-500 md:grid"><span>Entity</span><span>Tier</span><span>Data subjects</span><span>Status</span><span>Form</span></div>
        @forelse($forms as $form)
            <div class="grid min-w-[760px] grid-cols-[1fr_0.7fr_0.7fr_0.8fr_0.5fr] gap-4 border-b border-slate-100 px-5 py-5 last:border-0 md:items-center"><div><p class="font-semibold">{{ $form->entity_profile['entity_name'] ?? 'Unnamed entity' }}</p><p class="mt-1 text-xs text-slate-500">Saved {{ $form->created_at->format('d M Y, H:i') }}</p></div><p class="text-sm text-slate-600">{{ $form->tier }}</p><p class="text-sm text-slate-600">{{ number_format($form->data_subject_count) }}</p><div><x-status :label="ucfirst($form->status)" :tone="$form->status === 'submitted' ? 'green' : 'amber'" /></div><a href="{{ route('compliance.dp1.download', $form) }}" class="text-xs font-bold text-cyan-800 hover:underline">Download</a></div>
        @empty
            <div class="px-5 py-16 text-center"><p class="text-lg font-bold">No DP1 applications saved</p><p class="mt-2 text-sm text-slate-500">Complete the wizard to create your first application draft.</p><a href="{{ route('compliance.dp1.create') }}" class="button-primary mt-6">Start DP1 application</a></div>
        @endforelse
    </div>
</div>
@endsection
