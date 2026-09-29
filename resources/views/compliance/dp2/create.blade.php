@extends('layouts.app')
@section('content')
@php
    $selectedOrganizationId = old('organization_id', $activeOrganization?->id ?? $organizations->first()?->id);
    $selectedOrganization = $organizations->firstWhere('id', (int) $selectedOrganizationId) ?? $organizations->first();
    $selectedDpo = $selectedOrganization?->dpos->firstWhere('pivot.role', 'dpo') ?? $selectedOrganization?->dpos->first();
    $selectedDpoQualifications = implode("\n", $selectedDpo?->dpo_qualifications ?? []);
    $organizationPayload = $organizations->map(fn ($organization) => [
        'dpo' => ($dpo = $organization->dpos->firstWhere('pivot.role', 'dpo') ?? $organization->dpos->first()) ? [
            'name' => $dpo->name,
            'email' => $dpo->email,
            'registration_number' => $dpo->dpo_registration_number,
            'address' => $dpo->dpo_address,
            'qualifications' => implode("\n", $dpo->dpo_qualifications ?? []),
            'certification_status' => $dpo->dpo_certification_status,
            'reporting_line' => $dpo->dpo_reporting_line,
            'official_phone' => $dpo->dpo_official_phone,
            'mobile' => $dpo->dpo_mobile,
        ] : null,
        'id' => $organization->id,
        'name' => $organization->name,
        'registration_number' => $organization->registration_number,
        'physical_address' => $organization->physical_address,
        'postal_address' => $organization->postal_address,
        'telephone' => $organization->telephone,
        'fax' => $organization->fax,
        'email' => $organization->email,
        'business_scope' => $organization->business_scope,
    ])->values();
@endphp
<x-page-heading eyebrow="DP2 · New appointment" title="Record a DPO appointment" description="Capture the controller and appointed officer details required by the official POTRAZ DP2 notification form." />
<form method="POST" action="{{ route('compliance.dp2.store') }}" class="surface grid gap-8 p-6 lg:grid-cols-2 lg:p-8" data-organization-form>
    @csrf
    <div class="grid gap-5 lg:col-span-2">
        <h2 class="border-b border-slate-200 pb-3 font-bold">1. Client information</h2>
        <label class="grid gap-2">
            <span class="text-sm font-semibold text-slate-700">Organisation <span class="text-cyan-700">*</span></span>
            <select name="organization_id" required class="form-control" data-organization-select>
                @foreach($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((int) $selectedOrganizationId === $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
            @error('organization_id')<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
        </label>
        <div class="grid gap-5 md:grid-cols-2">
            <x-field name="controller_name" label="Name of data controller" required readonly data-org-field="name" :value="$selectedOrganization?->name" />
            <x-field name="controller_license_number" label="Data controller licence number" required readonly data-org-field="registration_number" :value="$selectedOrganization?->registration_number" />
            <x-textarea name="controller_physical_address" label="Physical address" required readonly data-org-field="physical_address" :value="$selectedOrganization?->physical_address" />
            <x-textarea name="controller_postal_address" label="Postal address" required readonly data-org-field="postal_address" :value="$selectedOrganization?->postal_address" />
            <x-field name="controller_telephone" label="Telephone / cell number" required readonly data-org-field="telephone" :value="$selectedOrganization?->telephone" />
            <x-field name="controller_fax" label="Fax number" readonly data-org-field="fax" :value="$selectedOrganization?->fax" />
            <x-field name="controller_email" label="Controller email" type="email" required readonly data-org-field="email" :value="$selectedOrganization?->email" />
            <x-textarea name="business_scope" label="Scope of business operations" required readonly data-org-field="business_scope" :value="$selectedOrganization?->business_scope" />
        </div>
    </div>
    <div class="grid gap-5 lg:col-span-2"><h2 class="border-b border-slate-200 pb-3 font-bold">2. Appointed data protection officer</h2><div class="grid gap-5 md:grid-cols-2"><x-field name="full_name" label="Name of appointed DPO" required data-dpo-field="name" :value="$selectedDpo?->name" /><x-field name="dpo_registration_number" label="DPO registration number" data-dpo-field="registration_number" :value="$selectedDpo?->dpo_registration_number" /><x-field name="official_email" label="Email of DPO" type="email" required data-dpo-field="email" :value="$selectedDpo?->email" /><x-field name="official_phone" label="DPO telephone number" required data-dpo-field="official_phone" :value="$selectedDpo?->dpo_official_phone" /><x-field name="dpo_mobile" label="DPO mobile number" required data-dpo-field="mobile" :value="$selectedDpo?->dpo_mobile" /><x-textarea name="dpo_address" label="Address of DPO" required data-dpo-field="address" :value="$selectedDpo?->dpo_address" /><x-textarea name="qualifications" label="Educational and professional qualifications" hint="One qualification per line." required rows="4" data-dpo-field="qualifications" :value="$selectedDpoQualifications" /><x-field name="certification_status" label="Certification status" hint="For example, certified / in progress" required data-dpo-field="certification_status" :value="$selectedDpo?->dpo_certification_status" /><x-field name="reporting_line" label="Direct organisational reporting line" required data-dpo-field="reporting_line" :value="$selectedDpo?->dpo_reporting_line" /><x-field name="appointed_at" label="Appointment date" type="date" required /></div></div>
    <div class="grid gap-5 lg:col-span-2"><h2 class="border-b border-slate-200 pb-3 font-bold">3. Declaration</h2><x-textarea name="appointment_declaration" label="Appointment declaration" required rows="5" /></div>
    <div class="flex justify-end gap-3 border-t border-slate-200 pt-6 lg:col-span-2"><a href="{{ route('compliance.dp2.index') }}" class="button-secondary">Cancel</a><button class="button-primary" type="submit">Save appointment</button></div>
</form>
@push('scripts')
<script>
    (() => {
        const root = document.querySelector('[data-organization-form]');
        if (!root) return;
        const select = root.querySelector('[data-organization-select]');
        const organizations = @json($organizationPayload);
        const fillOrganization = () => {
            const organization = organizations.find((item) => String(item.id) === select?.value);
            if (!organization) return;
            root.querySelectorAll('[data-org-field]').forEach((field) => {
                field.value = organization[field.dataset.orgField] || '';
            });
            root.querySelectorAll('[data-dpo-field]').forEach((field) => {
                field.value = organization.dpo?.[field.dataset.dpoField] || '';
            });
        };
        select?.addEventListener('change', fillOrganization);
        fillOrganization();
    })();
</script>
@endpush
@endsection
