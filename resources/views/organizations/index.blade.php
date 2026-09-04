@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DPO workspace · Client organisations" title="Your organisations" description="Manage every organisation you support and switch context before preparing a filing or reviewing an incident.">
    <x-slot:actions><a href="{{ route('compliance.organizations.create') }}" class="button-primary">Add organisation <span aria-hidden="true">+</span></a></x-slot:actions>
</x-page-heading>
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse($organizations as $organization)
        <div class="surface flex min-h-48 flex-col justify-between p-5 {{ $activeOrganization?->is($organization) ? 'border-cyan-500 ring-2 ring-cyan-100' : '' }}"><div><div class="flex items-start justify-between gap-4"><div><h2 class="font-bold">{{ $organization->name }}</h2><p class="mt-1 text-xs text-slate-500">{{ $organization->registration_number ?: 'Registration number not added' }}</p></div>@if($activeOrganization?->is($organization))<x-status label="Active" tone="green" />@endif</div><p class="mt-6 text-xs text-slate-500">Client workspace for DP1, DP2, ROPA, and DP3 tracking.</p></div>@if(!$activeOrganization?->is($organization))<form method="POST" action="{{ route('compliance.organizations.switch', $organization) }}" class="mt-5">@csrf<button class="button-secondary w-full" type="submit">Switch to this organisation</button></form>@else<a href="{{ route('compliance.dashboard') }}" class="button-primary mt-5 w-full">Open workspace</a>@endif</div>
    @empty
        <div class="surface px-5 py-16 text-center md:col-span-2 xl:col-span-3"><p class="text-lg font-bold">No client organisations yet</p><p class="mt-2 text-sm text-slate-500">Add the first organisation you support as a DPO.</p><a href="{{ route('compliance.organizations.create') }}" class="button-primary mt-6">Add organisation</a></div>
    @endforelse
</div>
@endsection
