@extends('layouts.app')
@section('content')
@php
    $selectedOrganizationId = old('organization_id', $organization?->id ?? $organizations->first()?->id);
    $selectedOrganization = $organizations->firstWhere('id', (int) $selectedOrganizationId) ?? $organizations->first();
    $organizationPayload = $organizations->map(fn ($organization) => [
        'id' => $organization->id,
        'name' => $organization->name,
        'email' => $organization->email,
        'physical_address' => $organization->physical_address,
        'postal_address' => $organization->postal_address,
    ])->values();
@endphp
<x-page-heading eyebrow="Privacy operations · Policy generator" title="Generate a privacy policy" description="Create a practical first draft for the active organisation. Review it with the organisation's legal and operational owners before publishing.">
    <x-slot:actions><x-status label="{{ $selectedOrganization?->name ?? 'No organisation' }}" tone="cyan" /></x-slot:actions>
</x-page-heading>
<form method="POST" action="{{ route('compliance.privacy-policies.download') }}" class="surface grid gap-8 p-6 lg:p-8" data-organization-form>
    @csrf
    <div class="grid gap-5 md:grid-cols-2">
        <label class="grid gap-2">
            <span class="text-sm font-semibold text-slate-700">Organisation <span class="text-cyan-700">*</span></span>
            <select name="organization_id" required class="form-control" data-organization-select>
                @foreach($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((int) $selectedOrganizationId === $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
            @error('organization_id')<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
        </label>
        <label class="grid gap-2">
            <span class="text-sm font-semibold text-slate-700">Policy type <span class="text-cyan-700">*</span></span>
            <select name="policy_type" required class="form-control">
                <option value="customer" @selected(old('policy_type') === 'customer')>Customer</option>
                <option value="employee" @selected(old('policy_type') === 'employee')>Employee</option>
                <option value="website" @selected(old('policy_type') === 'website')>Website</option>
            </select>
            @error('policy_type')<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
        </label>
        <x-field name="effective_date" label="Effective date" type="date" required />
        <x-field name="contact_email" label="Privacy contact email" type="email" required readonly data-org-field="email" :value="$selectedOrganization?->email" />
        <x-textarea name="contact_address" label="Privacy contact address" required readonly data-org-field="address" :value="$selectedOrganization?->physical_address ?: $selectedOrganization?->postal_address" />
    </div>
    <div class="grid gap-5 border-t border-slate-200 pt-6 md:grid-cols-2"><x-textarea name="processing_summary" label="What personal data is processed and why?" hint="List the main categories and purposes." required rows="6" /><x-textarea name="retention_summary" label="Retention and deletion approach" required rows="6" /></div>
    <div class="border-t border-slate-200 pt-6 text-sm leading-6 text-slate-500">This generator creates a customisable working draft. It is not legal advice and should be reviewed before publication.</div>
    <div class="flex justify-end"><button class="button-primary" type="submit">Generate privacy policy <span aria-hidden="true">↓</span></button></div>
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
                const key = field.dataset.orgField;
                field.value = key === 'address'
                    ? (organization.physical_address || organization.postal_address || '')
                    : (organization[key] || '');
            });
        };
        select?.addEventListener('change', fillOrganization);
        fillOrganization();
    })();
</script>
@endpush
@endsection
