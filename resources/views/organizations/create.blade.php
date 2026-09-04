@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DPO workspace · New client" title="Add an organisation" description="Create a separate client workspace. Your filings and incidents will remain isolated from every other organisation you support." />
<form method="POST" action="{{ route('compliance.organizations.store') }}" class="surface grid gap-6 p-6 lg:max-w-2xl lg:p-8">@csrf<x-field name="name" label="Organisation name" required /><x-field name="registration_number" label="Registration number" hint="Optional, but useful for DP1 and DP2 records." /><div class="flex justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('compliance.organizations.index') }}" class="button-secondary">Cancel</a><button class="button-primary" type="submit">Add organisation</button></div></form>
@endsection
