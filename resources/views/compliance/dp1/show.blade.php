@extends('layouts.app')

@section('content')
@php
    $entity = $form->entity_profile ?? [];
    $processing = $form->processing_details ?? [];
    $sensitive = $form->sensitive_data_details ?? [];
    $processors = $form->processors ?? [];
    $transfers = $form->cross_border_transfers ?? [];
    $security = $form->security_measures ?? [];
    $attachments = $form->attachments ?? [];
    $formId = $form->getKey();
    $rows = [
        'Entity name' => $entity['entity_name'] ?? null,
        'Registration number' => $entity['registration_number'] ?? null,
        'Legal structure' => $entity['legal_structure'] ?? null,
        'Business sector' => $entity['business_sector'] ?? null,
        'Physical address' => $entity['physical_address'] ?? null,
        'Phone number' => $entity['phone_number'] ?? null,
        'Email address' => $entity['email_address'] ?? null,
        'Website' => $entity['website'] ?? null,
        'DPO name' => $entity['dpo_name'] ?? null,
        'DPO phone' => $entity['dpo_phone'] ?? null,
        'DPO email' => $entity['dpo_email'] ?? null,
        'Representative name' => $entity['representative_name'] ?? null,
        'Representative phone' => $entity['representative_phone'] ?? null,
        'Representative address' => $entity['representative_address'] ?? null,
        'Representative email' => $entity['representative_email'] ?? null,
        'Representative website' => $entity['representative_website'] ?? null,
        'Data subject count' => number_format($form->data_subject_count),
        'Tier' => $form->tier,
        'Registration fee' => '$'.number_format($form->registration_fee, 2),
        'Application fee' => '$'.number_format($form->application_fee, 2),
        'Total fee' => '$'.number_format($form->total_fee, 2),
        'Data subject categories' => $processing['data_subject_categories'] ?? null,
        'Personal data types' => $processing['personal_data_types'] ?? null,
        'Purpose of processing' => $processing['processing_purpose'] ?? null,
        'Data recipients' => $processing['data_recipients'] ?? null,
        'Legal grounds' => $processing['legal_grounds'] ?? null,
        'Sensitive data details' => $sensitive['details'] ?? null,
        'Processor details' => $processors['details'] ?? null,
        'Cross-border transfers' => $transfers['details'] ?? null,
        'Risks to personal data' => $security['risks'] ?? null,
        'Security measures' => $security['details'] ?? null,
        'Declaration name' => $entity['declarant_name'] ?? null,
        'Declaration position' => $entity['declarant_position'] ?? null,
    ];
    $attachmentLabels = [
        'certificate_of_incorporation' => 'Certificate of Incorporation',
        'cr6_cr14' => 'CR6 / CR14',
        'tax_clearance' => 'Tax Clearance',
        'data_protection_policy' => 'Data Protection Policy',
        'signature_file' => 'Signature file',
    ];
@endphp

<x-page-heading eyebrow="DP1 · Preview" :title="$entity['entity_name'] ?? 'DP1 application'" description="Review the captured information before editing or downloading the official form.">
    <x-slot:actions>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('compliance.dp1.edit', ['dp1' => $formId]) }}" class="button-secondary">Edit application</a>
            <form method="POST" action="{{ route('compliance.dp1.send-potraz', ['formDp1' => $formId]) }}">
                @csrf
                <button type="submit" class="button-secondary">Send to POTRAZ</button>
            </form>
            <a href="{{ route('compliance.dp1.download', ['formDp1' => $formId]) }}" class="button-primary">Download DP1</a>
        </div>
    </x-slot:actions>
</x-page-heading>

<div class="grid gap-6 lg:grid-cols-[1fr_0.35fr]">
    <section class="surface overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-500">Application details</div>
        <dl class="divide-y divide-slate-100">
            @foreach($rows as $label => $value)
                <div class="grid gap-2 px-5 py-4 md:grid-cols-[0.32fr_1fr]">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                    <dd class="whitespace-pre-line text-sm text-slate-800">{{ filled($value) ? $value : 'Not provided' }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <aside class="surface self-start overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-500">Files</div>
        <div class="grid gap-3 p-5">
            @foreach($attachmentLabels as $key => $label)
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                    <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                    <x-status :label="isset($attachments[$key]) ? 'Attached' : 'Missing'" :tone="isset($attachments[$key]) ? 'green' : 'amber'" />
                </div>
            @endforeach
        </div>
    </aside>
</div>
@endsection
