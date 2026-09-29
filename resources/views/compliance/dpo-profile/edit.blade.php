@extends('layouts.app')

@section('content')
@php
    $qualifications = old('dpo_qualifications', implode("\n", $user->dpo_qualifications ?? []));
@endphp

<x-page-heading eyebrow="DPO profile" title="Your data protection officer details" description="Keep your officer details ready so DP2 appointment forms can be prepared quickly for each client organisation." />

<form method="POST" action="{{ route('compliance.dpo-profile.update') }}" class="surface grid gap-6 p-6 lg:grid-cols-2 lg:p-8">
    @csrf
    @method('PUT')

    <x-field name="name" label="Full name" required autocomplete="name" :value="$user->name" />
    <x-field name="email" label="Official email" type="email" required autocomplete="email" :value="$user->email" />
    <x-field name="dpo_registration_number" label="DPO registration number" :value="$user->dpo_registration_number" />
    <x-field name="dpo_official_phone" label="DPO telephone number" required :value="$user->dpo_official_phone" />
    <x-field name="dpo_mobile" label="DPO mobile number" required :value="$user->dpo_mobile" />
    <x-field name="dpo_certification_status" label="Certification status" hint="For example, certified / in progress" required :value="$user->dpo_certification_status" />
    <x-field name="dpo_reporting_line" label="Default reporting line" required :value="$user->dpo_reporting_line" />
    <x-textarea name="dpo_address" label="Address of DPO" required :value="$user->dpo_address" />
    <div class="lg:col-span-2">
        <x-textarea name="dpo_qualifications" label="Educational and professional qualifications" hint="One qualification per line." required rows="5" :value="$qualifications" />
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-200 pt-6 lg:col-span-2">
        <a href="{{ route('compliance.organizations.index') }}" class="button-secondary">Organisations</a>
        <button class="button-primary" type="submit">Save DPO profile</button>
    </div>
</form>
@endsection
