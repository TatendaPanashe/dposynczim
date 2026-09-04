@extends('layouts.app')

@section('content')
<div data-dp1-wizard>
    <x-page-heading eyebrow="DP1 · Data controller licensing" title="Prepare your DP1 application" description="Work through the seven POTRAZ sections. Your fee tier updates as you estimate the number of data subjects." />

    <div class="mb-8 grid gap-2 sm:grid-cols-4">
        @foreach(['Entity profile', 'Processing grounds', 'Safeguards', 'Attachments'] as $index => $label)
            <button type="button" data-dp1-step="{{ $index }}" class="border-b-2 px-2 py-3 text-left text-xs font-bold {{ $index === 0 ? 'border-cyan-600 text-cyan-800' : 'border-slate-200 text-slate-400' }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} &nbsp; {{ $label }}</button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('compliance.dp1.store') }}" enctype="multipart/form-data" class="surface overflow-hidden">
        @csrf
        <section data-dp1-panel="0" class="grid gap-6 p-6 lg:grid-cols-2 lg:p-8"><div class="lg:col-span-2"><h2 class="text-xl font-bold">1. Entity profile</h2><p class="mt-1 text-sm text-slate-500">Tell POTRAZ who controls the processing.</p></div><x-field name="entity_name" label="Entity name" required /><x-field name="registration_number" label="Registration number" /><x-field name="business_sector" label="Business sector" required /><x-field name="legal_structure" label="Legal structure" required /><x-textarea name="physical_address" label="Physical address" required /></section>
        <section data-dp1-panel="1" class="hidden grid gap-6 p-6 lg:grid-cols-2 lg:p-8"><div class="lg:col-span-2"><h2 class="text-xl font-bold">2. Data subjects and processing grounds</h2><p class="mt-1 text-sm text-slate-500">Describe the populations, data, purpose, and lawful basis.</p></div><x-field name="data_subject_count" label="Estimated data subjects" type="number" hint="Minimum DP1 threshold: 50" required min="50" data-dp1-subjects /><div class="border border-cyan-200 bg-cyan-50 p-5"><p class="text-xs font-bold uppercase tracking-wide text-cyan-800">Live fee estimate</p><p class="mt-2 text-2xl font-bold" data-dp1-fee-tier>Tier 1</p><p class="mt-1 text-sm text-cyan-900">Registration $<span data-dp1-registration>50.00</span> · Application $<span data-dp1-application>0.00</span></p><p class="mt-2 text-sm font-bold text-cyan-950">Total $<span data-dp1-total>50.00</span></p></div><x-textarea name="data_subject_categories" label="Categories of data subjects" required /><x-textarea name="personal_data_types" label="Types of personal data" required /><x-textarea name="legal_grounds" label="Legal grounds for processing" required /><x-textarea name="data_recipients" label="Data recipients" /></section>
        <section data-dp1-panel="2" class="hidden grid gap-6 p-6 lg:p-8"><div><h2 class="text-xl font-bold">3-6. Processing safeguards</h2><p class="mt-1 text-sm text-slate-500">Include sensitive data, processors, transfers, and security measures.</p></div><x-textarea name="sensitive_data_details" label="Sensitive data and special legal grounds" /><x-textarea name="processor_details" label="Data processors and contractual safeguards" /><x-textarea name="cross_border_transfers" label="Cross-border destinations and transfer mechanisms" /><x-textarea name="security_measures" label="Security measures and safeguards" required /></section>
        <section data-dp1-panel="3" class="hidden grid gap-6 p-6 lg:p-8"><div><h2 class="text-xl font-bold">7. Supporting documents</h2><p class="mt-1 text-sm text-slate-500">The Certificate of Incorporation is required. Add the remaining documents when available.</p></div><div class="grid gap-5 md:grid-cols-2"><x-field name="certificate_of_incorporation" label="Certificate of Incorporation" type="file" required accept=".pdf,.jpg,.jpeg,.png" /><x-field name="cr6_cr14" label="CR6 / CR14" type="file" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="tax_clearance" label="Tax Clearance" type="file" accept=".pdf,.jpg,.jpeg,.png" /><x-field name="data_protection_policy" label="Data Protection Policy" type="file" accept=".pdf,.jpg,.jpeg,.png" /></div></section>
        <footer class="flex justify-between border-t border-slate-200 bg-slate-50 px-6 py-4 lg:px-8"><button type="button" data-dp1-back class="button-secondary hidden">Back</button><span data-dp1-spacer></span><button type="button" data-dp1-next class="button-primary">Continue <span aria-hidden="true">→</span></button><button type="submit" data-dp1-submit class="button-primary hidden">Save DP1 draft</button></footer>
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
        const subjectInput = root.querySelector('[data-dp1-subjects]');
        let current = 0;

        const updateFee = () => {
            const count = Number(subjectInput?.value || 50);
            const fee = count <= 1000 ? ['Tier 1', '50.00', '0.00', '50.00'] : count <= 100000 ? ['Tier 2', '300.00', '30.00', '330.00'] : count <= 500000 ? ['Tier 3', '500.00', '30.00', '530.00'] : ['Tier 4', '2500.00', '30.00', '2530.00'];
            root.querySelector('[data-dp1-fee-tier]').textContent = fee[0];
            root.querySelector('[data-dp1-registration]').textContent = fee[1];
            root.querySelector('[data-dp1-application]').textContent = fee[2];
            root.querySelector('[data-dp1-total]').textContent = fee[3];
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
        updateFee();
        render();
    })();
</script>
@endpush
@endsection