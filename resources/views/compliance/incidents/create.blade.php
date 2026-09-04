@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DP3 · New incident" title="Log a personal data breach" description="Record the facts you know now. You can expand the response as your investigation develops." />
<form method="POST" action="{{ route('compliance.incidents.store') }}" class="surface grid gap-8 p-6 lg:p-8">
    @csrf
    <div class="grid gap-5 lg:grid-cols-2"><x-field name="title" label="Short incident title" required /><x-field name="severity" label="Severity" required list="severity-options" /><datalist id="severity-options"><option value="low"><option value="medium"><option value="high"><option value="critical"></datalist><x-field name="occurred_at" label="When did it occur?" type="datetime-local" required /><x-field name="detected_at" label="When was it detected?" type="datetime-local" hint="The 24-hour DP3 timer starts here." required /></div>
    <div class="grid gap-5 border-t border-slate-200 pt-6 lg:grid-cols-2"><x-textarea name="description" label="What happened?" required rows="7" /><x-textarea name="affected_data_subjects" label="Affected data subjects or records" hint="One category or estimate per line." required rows="7" /></div>
    <div class="flex justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('compliance.incidents.index') }}" class="button-secondary">Cancel</a><button class="button-primary" type="submit">Start response clock</button></div>
</form>
@endsection