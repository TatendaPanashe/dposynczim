@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="ROPA · New activity" title="Add a processing activity" description="Capture the same fields that appear in the DPA ROPA register export." />
<form method="POST" action="{{ route('compliance.ropa.store') }}" class="surface grid gap-8 p-6 lg:p-8">
    @csrf

    <section class="grid gap-5">
        <div>
            <h2 class="text-base font-bold text-slate-950">Activity and purpose</h2>
            <p class="mt-1 text-sm text-slate-500">Columns B to G in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-field name="business_function" label="Business function / department / team" />
            <x-field name="owner" label="Information owner" required />
            <x-textarea name="storage_location" label="Where is it stored / application name" rows="3" />
            <x-textarea name="processing_activity" label="What is it / information held" rows="3" required />
            <x-textarea name="purpose" label="Why do we have it / purpose of processing" rows="3" required />
            <x-field name="legal_basis" label="Grounds / lawful basis for processing PII" required />
        </div>
    </section>

    <section class="grid gap-5 border-t border-slate-200 pt-6">
        <div>
            <h2 class="text-base font-bold text-slate-950">Controller and data subjects</h2>
            <p class="mt-1 text-sm text-slate-500">Columns H to J in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-field name="controller_name" label="Name of controller or joint controller" />
            <x-field name="representative_name" label="Representative if controller is outside Zimbabwe" />
            <x-textarea name="data_subject_categories" label="Categories of data subjects" hint="One category per line." required />
        </div>
    </section>

    <section class="grid gap-5 border-t border-slate-200 pt-6">
        <div>
            <h2 class="text-base font-bold text-slate-950">Retention and data categories</h2>
            <p class="mt-1 text-sm text-slate-500">Columns K to M in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-field name="retention_period" label="How long do we keep it" required />
            <x-textarea name="retention_basis" label="Basis for the retention period" rows="3" />
            <x-field name="data_classification" label="Data classification" />
            <x-textarea name="personal_data_categories" label="Categories of personal data" hint="One category per line." required />
        </div>
    </section>

    <section class="grid gap-5 border-t border-slate-200 pt-6">
        <div>
            <h2 class="text-base font-bold text-slate-950">Recipients and processors</h2>
            <p class="mt-1 text-sm text-slate-500">Columns N to P in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-textarea name="recipients" label="Categories of recipients" hint="One recipient per line." />
            <x-field name="processor_name" label="Name of processor" />
            <x-textarea name="third_party_agreement" label="Data processing / data sharing agreement" rows="3" />
        </div>
    </section>

    <section class="grid gap-5 border-t border-slate-200 pt-6">
        <div>
            <h2 class="text-base font-bold text-slate-950">Security and transfers</h2>
            <p class="mt-1 text-sm text-slate-500">Columns Q to S in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-textarea name="security_measures" label="Technical and organisational security measures" hint="One safeguard per line." required />
            <x-textarea name="transfer_security_measures" label="Additional security measures for international transfers" />
            <x-textarea name="protection_assessment" label="Adequate level of protection assessment" />
        </div>
    </section>

    <section class="grid gap-5 border-t border-slate-200 pt-6">
        <div>
            <h2 class="text-base font-bold text-slate-950">Collection, consent, and assessment</h2>
            <p class="mt-1 text-sm text-slate-500">Columns T to X in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-textarea name="data_collection_method" label="How is the data collected" rows="3" />
            <x-textarea name="consent_evidence" label="Consent evidence and withdrawal method" rows="3" />
            <x-textarea name="legitimate_interest_assessment" label="Legitimate interest assessment" rows="3" />
            <x-field name="data_volume" label="Volume of data" />
            <x-textarea name="dpia_record" label="Record of DPIA" rows="3" />
        </div>
    </section>

    <section class="grid gap-5 border-t border-slate-200 pt-6">
        <div>
            <h2 class="text-base font-bold text-slate-950">Risk and actions</h2>
            <p class="mt-1 text-sm text-slate-500">Columns Y to AF in the register.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <x-textarea name="data_risks" label="Risks to data subjects" rows="3" />
            <x-field name="risk_impact" label="Risk impact on data subject" />
            <x-textarea name="data_breaches" label="Data breaches suspected or encountered" rows="3" />
            <x-textarea name="breach_notification" label="Notified to Authority / data subjects" rows="3" />
            <x-textarea name="risk_actions" label="Actions to reduce or manage risk" rows="3" />
            <x-field name="action_owner" label="Action owner" />
            <x-field name="action_due_date" label="Date to be completed" type="date" />
            <x-field name="reviewed_at" label="Date last assessed" type="date" />
        </div>
    </section>

    <div class="flex justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('compliance.ropa.index') }}" class="button-secondary">Cancel</a>
        <button class="button-primary" type="submit">Save activity</button>
    </div>
</form>
@endsection
