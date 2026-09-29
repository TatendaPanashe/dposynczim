@extends('layouts.app')

@section('content')
@php
    $entity = $form?->entity_profile ?? [];
    $processing = $form?->processing_details ?? [];
    $sensitive = $form?->sensitive_data_details ?? [];
    $processors = $form?->processors ?? [];
    $transfers = $form?->cross_border_transfers ?? [];
    $security = $form?->security_measures ?? [];
    $attachments = $form?->attachments ?? [];
    $selectedOrganizationId = old('organization_id', $form?->organization_id ?? $activeOrganization?->id ?? $organizations->first()?->id);
    $selectedOrganization = $organizations->firstWhere('id', (int) $selectedOrganizationId) ?? $organizations->first();
    $selectedDpoAppointment = $selectedOrganization?->activeDpoAppointment;
    $isEditing = $form !== null;
    $organizationPayload = $organizations->map(fn ($organization) => [
        'id' => $organization->id,
        'name' => $organization->name,
        'registration_number' => $organization->registration_number,
        'business_sector' => $organization->business_sector,
        'legal_structure' => $organization->legal_structure,
        'physical_address' => $organization->physical_address,
        'email_address' => $organization->email ?? '',
        'phone_number' => $organization->telephone ?? '',
        'website' => '',
        'dpo_name' => $organization->activeDpoAppointment?->full_name ?? '',
        'dpo_phone' => $organization->activeDpoAppointment?->official_phone ?? $organization->activeDpoAppointment?->dpo_mobile ?? '',
        'dpo_email' => $organization->activeDpoAppointment?->official_email ?? '',
    ])->values();
@endphp
<div data-dp1-wizard>
    <x-page-heading eyebrow="DP1 · Data controller licensing" :title="$isEditing ? 'Edit DP1 application' : 'Prepare your DP1 application'" description="Work through the seven POTRAZ sections. Your fee tier updates as you estimate the number of data subjects." />

    <div class="mb-8 grid gap-2 sm:grid-cols-4">
        @foreach(['Entity profile', 'Processing grounds', 'Safeguards', 'Attachments'] as $index => $label)
            <button type="button" data-dp1-step="{{ $index }}" class="border-b-2 px-2 py-3 text-left text-xs font-bold {{ $index === 0 ? 'border-cyan-600 text-cyan-800' : 'border-slate-200 text-slate-400' }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} &nbsp; {{ $label }}</button>
        @endforeach
    </div>

    <form method="POST" action="{{ $isEditing ? route('compliance.dp1.update', $form) : route('compliance.dp1.store') }}" enctype="multipart/form-data" class="surface overflow-hidden">
        @csrf
        @if($isEditing)
            @method('PATCH')
        @endif
        <section data-dp1-panel="0" class="grid gap-6 p-6 lg:grid-cols-2 lg:p-8">
            <div class="lg:col-span-2"><h2 class="text-xl font-bold">1. Entity profile</h2><p class="mt-1 text-sm text-slate-500">Tell POTRAZ who controls the processing.</p></div>
            <label class="grid gap-2 lg:col-span-2">
                <span class="text-sm font-semibold text-slate-700">Organisation <span class="text-cyan-700">*</span></span>
                <select name="organization_id" required class="form-control" data-organization-select>
                    @foreach($organizations as $organization)
                        <option value="{{ $organization->id }}" @selected((int) $selectedOrganizationId === $organization->id)>{{ $organization->name }}</option>
                    @endforeach
                </select>
                @error('organization_id')<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
            </label>
            <x-field name="entity_name" label="Entity name" required readonly data-org-field="name" :value="$entity['entity_name'] ?? $selectedOrganization?->name" />
            <x-field name="registration_number" label="Registration number" readonly data-org-field="registration_number" :value="$entity['registration_number'] ?? $selectedOrganization?->registration_number" />
            <x-field name="business_sector" label="Business sector" required readonly data-org-field="business_sector" :value="$entity['business_sector'] ?? $selectedOrganization?->business_sector" />
            <x-field name="legal_structure" label="Legal structure" required readonly data-org-field="legal_structure" :value="$entity['legal_structure'] ?? $selectedOrganization?->legal_structure" />
            <x-textarea name="physical_address" label="Physical address" required readonly data-org-field="physical_address" :value="$entity['physical_address'] ?? $selectedOrganization?->physical_address" />
            <x-field name="phone_number" label="Phone number" data-org-field="phone_number" :value="$entity['phone_number'] ?? $selectedOrganization?->telephone" />
            <x-field name="email_address" label="Email address" type="email" data-org-field="email_address" :value="$entity['email_address'] ?? $selectedOrganization?->email" />
            <x-field name="website" label="Website" data-org-field="website" :value="$entity['website'] ?? null" />
            <div class="lg:col-span-2"><h3 class="text-base font-bold">Designated Data Protection Officer</h3></div>
            <x-field name="dpo_name" label="DPO name" data-org-field="dpo_name" :value="$entity['dpo_name'] ?? $selectedDpoAppointment?->full_name" />
            <x-field name="dpo_phone" label="DPO phone number" data-org-field="dpo_phone" :value="$entity['dpo_phone'] ?? $selectedDpoAppointment?->official_phone ?? $selectedDpoAppointment?->dpo_mobile" />
            <x-field name="dpo_email" label="DPO email address" type="email" data-org-field="dpo_email" :value="$entity['dpo_email'] ?? $selectedDpoAppointment?->official_email" />
            <div class="lg:col-span-2"><h3 class="text-base font-bold">Representative in Zimbabwe</h3></div>
            <x-field name="representative_name" label="Representative name" :value="$entity['representative_name'] ?? null" />
            <x-field name="representative_phone" label="Representative phone number" :value="$entity['representative_phone'] ?? null" />
            <x-textarea name="representative_address" label="Representative address" :value="$entity['representative_address'] ?? null" />
            <x-field name="representative_email" label="Representative email" type="email" :value="$entity['representative_email'] ?? null" />
            <x-field name="representative_website" label="Representative website" :value="$entity['representative_website'] ?? null" />
        </section>
        <section data-dp1-panel="1" class="hidden grid gap-6 p-6 lg:grid-cols-2 lg:p-8"><div class="lg:col-span-2"><h2 class="text-xl font-bold">2. Data subjects and processing grounds</h2><p class="mt-1 text-sm text-slate-500">Describe the populations, data, purpose, and lawful basis.</p></div><x-field name="data_subject_count" label="Estimated data subjects" type="number" hint="Minimum DP1 threshold: 50" required min="50" data-dp1-subjects :value="$form?->data_subject_count" /><div class="border border-cyan-200 bg-cyan-50 p-5"><p class="text-xs font-bold uppercase tracking-wide text-cyan-800">Live fee estimate</p><p class="mt-2 text-2xl font-bold" data-dp1-fee-tier>Tier 1</p><p class="mt-1 text-sm text-cyan-900">Registration $<span data-dp1-registration>50.00</span> · Application $<span data-dp1-application>30.00</span></p><p class="mt-2 text-sm font-bold text-cyan-950">Total $<span data-dp1-total>80.00</span></p></div><x-textarea name="data_subject_categories" label="Categories of data subjects" required :value="$processing['data_subject_categories'] ?? null" /><x-textarea name="personal_data_types" label="Types of personal data" required :value="$processing['personal_data_types'] ?? null" /><x-textarea name="processing_purpose" label="Purpose of processing" required :value="$processing['processing_purpose'] ?? null" /><x-textarea name="data_recipients" label="Data recipients" :value="$processing['data_recipients'] ?? null" /><x-textarea name="legal_grounds" label="Legal grounds for processing" required :value="$processing['legal_grounds'] ?? null" /></section>
        <section data-dp1-panel="2" class="hidden grid gap-6 p-6 lg:p-8"><div><h2 class="text-xl font-bold">3-6. Processing safeguards</h2><p class="mt-1 text-sm text-slate-500">Include sensitive data, processors, transfers, and security measures.</p></div><x-textarea name="sensitive_data_details" label="Sensitive data and special legal grounds" :value="$sensitive['details'] ?? null" /><x-textarea name="processor_details" label="Data processors and contractual safeguards" :value="$processors['details'] ?? null" /><x-textarea name="cross_border_transfers" label="Cross-border destinations and transfer mechanisms" :value="$transfers['details'] ?? null" /><x-textarea name="data_risks" label="Risks to personal data" :value="$security['risks'] ?? null" /><x-textarea name="security_measures" label="Security measures and safeguards" required :value="$security['details'] ?? null" /></section>
        <section data-dp1-panel="3" class="hidden grid gap-6 p-6 lg:p-8"><div><h2 class="text-xl font-bold">7. Supporting documents</h2><p class="mt-1 text-sm text-slate-500">Upload replacements only when a saved file needs to change.</p></div><div class="grid gap-5 md:grid-cols-2"><x-field name="certificate_of_incorporation" label="Certificate of Incorporation" type="file" :required="! $isEditing && ! isset($attachments['certificate_of_incorporation'])" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="cr6_cr14" label="CR6 / CR14" type="file" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="tax_clearance" label="Tax Clearance" type="file" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="data_protection_policy" label="Data Protection Policy" type="file" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="signature_file" label="Signature file" type="file" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="declarant_name" label="Declaration name" :value="$entity['declarant_name'] ?? null" /><x-field name="declarant_position" label="Declaration position" :value="$entity['declarant_position'] ?? null" /></div></section>
        <footer class="flex justify-between border-t border-slate-200 bg-slate-50 px-6 py-4 lg:px-8"><button type="button" data-dp1-back class="button-secondary hidden">Back</button><span data-dp1-spacer></span><button type="button" data-dp1-next class="button-primary">Continue <span aria-hidden="true">→</span></button><button type="submit" data-dp1-submit class="button-primary hidden">{{ $isEditing ? 'Update and preview' : 'Save and preview' }}</button></footer>
    </form>
</div>
@push('scripts')
<script>
    (() => {
        const root = document.querySelector('[data-dp1-wizard]');
        if (!root) return;
        const panels = [...root.querySelectorAll('[data-dp1-panel]')];
        const steps = [...root.querySelectorAll('[data-dp1-step]')];
        const back = root.querySelector('[data-dp1-back]');
        const next = root.querySelector('[data-dp1-next]');
        const submit = root.querySelector('[data-dp1-submit]');
        const spacer = root.querySelector('[data-dp1-spacer]');
        const subjectInput = root.querySelector('input[name="data_subject_count"]');
        const organizationSelect = root.querySelector('[data-organization-select]');
        const organizations = @json($organizationPayload);
        let current = 0;

        const updateFee = () => {
            const count = Math.max(Number.parseInt(subjectInput?.value || '50', 10) || 50, 50);
            const fee = count <= 1000 ? ['Tier 1', '50.00', '30.00', '80.00'] : count <= 100000 ? ['Tier 2', '300.00', '30.00', '330.00'] : count <= 500000 ? ['Tier 3', '500.00', '30.00', '530.00'] : ['Tier 4', '2500.00', '30.00', '2530.00'];
            root.querySelector('[data-dp1-fee-tier]').textContent = fee[0];
            root.querySelector('[data-dp1-registration]').textContent = fee[1];
            root.querySelector('[data-dp1-application]').textContent = fee[2];
            root.querySelector('[data-dp1-total]').textContent = fee[3];
        };
        const fillOrganization = () => {
            if (@json($isEditing)) return;
            const organization = organizations.find((item) => String(item.id) === organizationSelect?.value);
            if (!organization) return;
            root.querySelectorAll('[data-org-field]').forEach((field) => {
                field.value = organization[field.dataset.orgField] || '';
            });
        };
        const render = () => {
            panels.forEach((panel, index) => panel.classList.toggle('hidden', index !== current));
            steps.forEach((step, index) => step.className = `border-b-2 px-2 py-3 text-left text-xs font-bold ${index === current ? 'border-cyan-600 text-cyan-800' : 'border-slate-200 text-slate-400'}`);
            back.classList.toggle('hidden', current === 0);
            spacer.classList.toggle('hidden', current !== 0);
            next.classList.toggle('hidden', current === panels.length - 1);
            submit.classList.toggle('hidden', current !== panels.length - 1);
        };
        steps.forEach((step, index) => step.addEventListener('click', () => { current = index; render(); }));
        back.addEventListener('click', () => { current--; render(); });
        next.addEventListener('click', () => { current++; render(); });
        subjectInput?.addEventListener('input', updateFee);
        organizationSelect?.addEventListener('change', fillOrganization);
        fillOrganization();
        updateFee();
        render();
    })();
</script>
@endpush
@endsection
