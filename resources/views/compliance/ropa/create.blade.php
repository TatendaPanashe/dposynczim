@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="ROPA · New activity" title="Add a processing activity" description="Use plain operational language. One item per line works well for categories, recipients, and safeguards." />
<form method="POST" action="{{ route('compliance.ropa.store') }}" class="surface grid gap-8 p-6 lg:p-8">
    @csrf
    <div class="grid gap-5 lg:grid-cols-2"><x-field name="processing_activity" label="Processing activity" hint="For example, customer onboarding" required /><x-field name="legal_basis" label="Legal basis" hint="Consent, contract, legal obligation, legitimate interest" required /><x-field name="retention_period" label="Retention period" required /><x-field name="owner" label="Business owner" required /></div>
    <div class="grid gap-5 border-t border-slate-200 pt-6 lg:grid-cols-2"><x-textarea name="purpose" label="Purpose of processing" required /><x-textarea name="data_subject_categories" label="Data subject categories" hint="One category per line." required /><x-textarea name="personal_data_categories" label="Personal data categories" hint="One category per line." required /><x-textarea name="recipients" label="Recipients and disclosures" hint="One recipient per line." /><x-textarea name="security_measures" label="Security measures" hint="One safeguard per line." required /><x-textarea name="cross_border_transfer" label="Cross-border transfer details" /></div>
    <div class="flex justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('compliance.ropa.index') }}" class="button-secondary">Cancel</a><button class="button-primary" type="submit">Save activity</button></div>
</form>
@endsection