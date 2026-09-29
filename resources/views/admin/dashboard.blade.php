@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Platform admin" title="Admin overview" description="Cross-organisation visibility for payments, users, organisations, and submitted compliance records.">
    <x-slot:actions><x-status label="DPOSync Zim" tone="cyan" /></x-slot:actions>
</x-page-heading>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Users</span><p class="mt-6 text-3xl font-bold">{{ $totals['users'] }}</p></div>
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Organisations</span><p class="mt-6 text-3xl font-bold">{{ $totals['organizations'] }}</p></div>
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Failed payments</span><p class="mt-6 text-3xl font-bold">{{ $totals['failed_payments'] }}</p></div>
    <div class="surface p-5"><span class="text-xs font-bold uppercase tracking-wide text-slate-500">Reports</span><p class="mt-6 text-3xl font-bold">{{ $totals['reports'] }}</p></div>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-2">
    <div class="surface">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-bold">Failed payments</h2></div>
        <div class="divide-y divide-slate-100">
            @forelse($failedPayments as $payment)
                <div class="grid gap-2 px-5 py-4 text-sm md:grid-cols-[1fr_0.8fr_0.6fr]">
                    <div><p class="font-semibold">{{ $payment->organization?->name ?? 'No organisation' }}</p><p class="mt-1 text-xs text-slate-500">{{ $payment->user?->email ?? 'No user' }}</p></div>
                    <p class="font-mono text-xs text-slate-500">{{ $payment->merchant_reference }}</p>
                    <p class="font-semibold">{{ $payment->currency }} {{ number_format($payment->amount_cents / 100, 2) }}</p>
                </div>
            @empty
                <p class="px-5 py-10 text-sm text-slate-500">No failed payments recorded.</p>
            @endforelse
        </div>
    </div>

    <div class="surface">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-bold">Users</h2></div>
        <div class="divide-y divide-slate-100">
            @foreach($users as $user)
                <div class="grid gap-2 px-5 py-4 text-sm md:grid-cols-[1fr_0.8fr_0.4fr]">
                    <div><p class="font-semibold">{{ $user->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p></div>
                    <p class="text-slate-600">{{ $user->organization?->name ?? 'No organisation' }}</p>
                    <x-status :label="$user->isAdmin() ? 'Admin' : 'User'" :tone="$user->isAdmin() ? 'cyan' : 'amber'" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-2">
    <div class="surface">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-bold">Organisations</h2></div>
        <div class="divide-y divide-slate-100">
            @foreach($organizations as $organization)
                <div class="grid gap-2 px-5 py-4 text-sm md:grid-cols-[1fr_0.4fr_0.4fr]">
                    <div><p class="font-semibold">{{ $organization->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $organization->registration_number ?: 'No registration number' }}</p></div>
                    <p>{{ $organization->users_count }} users</p>
                    <p>{{ $organization->dp1s_count }} DP1s</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="surface">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-bold">Recent payments</h2></div>
        <div class="divide-y divide-slate-100">
            @foreach($payments as $payment)
                <div class="grid gap-2 px-5 py-4 text-sm md:grid-cols-[1fr_0.5fr_0.5fr]">
                    <div><p class="font-semibold">{{ $payment->organization?->name ?? 'No organisation' }}</p><p class="mt-1 text-xs text-slate-500">{{ $payment->created_at->format('d M Y, H:i') }}</p></div>
                    <x-status :label="$payment->status" :tone="$payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'amber')" />
                    <p class="font-semibold">{{ $payment->currency }} {{ number_format($payment->amount_cents / 100, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mt-8 surface">
    <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-bold">Reports</h2></div>
    <div class="grid gap-6 p-5 xl:grid-cols-4">
        @foreach(['DP1' => $dp1Reports, 'DP2' => $dp2Reports, 'ROPA' => $ropaReports, 'Incidents' => $incidentReports] as $label => $reports)
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</h3>
                <div class="mt-3 grid gap-3">
                    @forelse($reports as $report)
                        <div class="border border-slate-200 p-3 text-sm">
                            <p class="font-semibold">{{ $report->organization?->name ?? 'No organisation' }}</p>
                            <p class="mt-1 text-xs text-slate-500">Saved {{ $report->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No records yet.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
