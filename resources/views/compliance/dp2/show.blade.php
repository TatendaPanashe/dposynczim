@extends('layouts.app')

@section('content')
@php
    $rows = [
        'Data controller' => $form->controller_name,
        'Licence number' => $form->controller_license_number,
        'Physical address' => $form->controller_physical_address,
        'Postal address' => $form->controller_postal_address,
        'Telephone / cell number' => $form->controller_telephone,
        'Fax number' => $form->controller_fax,
        'Controller email' => $form->controller_email,
        'Scope of business operations' => $form->business_scope,
        'Appointed DPO' => $form->full_name,
        'DPO registration number' => $form->dpo_registration_number,
        'DPO email' => $form->official_email,
        'DPO telephone' => $form->official_phone,
        'DPO mobile' => $form->dpo_mobile,
        'DPO address' => $form->dpo_address,
        'Qualifications' => implode("\n", $form->qualifications ?? []),
        'Certification status' => $form->certification_status,
        'Reporting line' => $form->reporting_line,
        'Appointment date' => $form->appointed_at?->format('d M Y'),
        'Declaration' => $form->appointment_declaration,
    ];
@endphp

<x-page-heading eyebrow="DP2 · Preview" :title="$form->full_name" description="Review the DPO appointment details before downloading the official form.">
    <x-slot:actions>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('compliance.dp2.index') }}" class="button-secondary">Appointment register</a>
            <form method="POST" action="{{ route('compliance.dp2.send-potraz', ['formDp2' => $form->getKey()]) }}">
                @csrf
                <button type="submit" class="button-secondary">Send to POTRAZ</button>
            </form>
            <a href="{{ route('compliance.dp2.download', ['formDp2' => $form->getKey()]) }}" class="button-primary">Download DP2</a>
        </div>
    </x-slot:actions>
</x-page-heading>

<section class="surface overflow-hidden">
    <div class="border-b border-slate-200 bg-slate-50 px-5 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-500">Appointment details</div>
    <dl class="divide-y divide-slate-100">
        @foreach($rows as $label => $value)
            <div class="grid gap-2 px-5 py-4 md:grid-cols-[0.32fr_1fr]">
                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                <dd class="whitespace-pre-line text-sm text-slate-800">{{ filled($value) ? $value : 'Not provided' }}</dd>
            </div>
        @endforeach
    </dl>
</section>
@endsection
