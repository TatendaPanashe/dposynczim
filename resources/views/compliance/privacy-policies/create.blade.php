@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="Privacy operations · Policy generator" title="Generate a privacy policy" description="Create a practical first draft for the active organisation. Review it with the organisation's legal and operational owners before publishing.">
    <x-slot:actions><x-status label="{{ auth()->user()->activeOrganization()?->name ?? 'No organisation' }}" tone="cyan" /></x-slot:actions>
</x-page-heading>
<form method="POST" action="{{ route('compliance.privacy-policies.download') }}" class="surface grid gap-8 p-6 lg:p-8">
    @csrf
    <div class="grid gap-5 md:grid-cols-2"><x-field name="policy_type" label="Policy type" required list="policy-types" /><datalist id="policy-types"><option value="customer"><option value="employee"><option value="website"></datalist><x-field name="effective_date" label="Effective date" type="date" required /><x-field name="contact_email" label="Privacy contact email" type="email" required /><x-textarea name="contact_address" label="Privacy contact address" required /></div>
    <div class="grid gap-5 border-t border-slate-200 pt-6 md:grid-cols-2"><x-textarea name="processing_summary" label="What personal data is processed and why?" hint="List the main categories and purposes." required rows="6" /><x-textarea name="retention_summary" label="Retention and deletion approach" required rows="6" /></div>
    <div class="border-t border-slate-200 pt-6 text-sm leading-6 text-slate-500">This generator creates a customisable working draft. It is not legal advice and should be reviewed before publication.</div>
    <div class="flex justify-end"><button class="button-primary" type="submit">Generate privacy policy <span aria-hidden="true">↓</span></button></div>
</form>
@endsection