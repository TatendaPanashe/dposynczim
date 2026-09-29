@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="DPO workspace · New client" title="Add an organisation" description="Create a separate client workspace. Your filings and incidents will remain isolated from every other organisation you support." />
<form method="POST" action="{{ route('compliance.organizations.store') }}" class="surface grid gap-6 p-6 lg:max-w-4xl lg:grid-cols-2 lg:p-8">
    @csrf
    <x-field name="name" label="Organisation name" required />
    <x-field name="registration_number" label="Registration number" hint="Used for DP1 and DP2 records." />
    <x-field name="business_sector" label="Business sector" />
    <x-field name="legal_structure" label="Legal structure" />
    <x-field name="telephone" label="Telephone / cell number" />
    <x-field name="fax" label="Fax number" />
    <x-field name="email" label="Organisation email" type="email" />
    <x-textarea name="physical_address" label="Physical address" />
    <x-textarea name="postal_address" label="Postal address" />
    <x-textarea name="business_scope" label="Scope of business operations" rows="5" />
    <div class="flex justify-end gap-3 border-t border-slate-200 pt-6 lg:col-span-2"><a href="{{ route('compliance.organizations.index') }}" class="button-secondary">Cancel</a><button class="button-primary" type="submit">Add organisation</button></div>
</form>
@endsection
