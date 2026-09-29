@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Compliance forms" :title="$form ? 'Edit compliance form' : 'Add compliance form'" description="Record consent evidence, cross-border transfer authorisations, and general compliance forms for the selected organisation." />

<form method="POST" action="{{ $form ? route('compliance.forms.update', ['form' => $form]) : route('compliance.forms.store') }}" class="surface grid gap-6 p-6 lg:grid-cols-2 lg:p-8">
    @csrf
    @if($form)
        @method('PUT')
    @endif

    <x-field name="title" label="Form title" required :value="old('title', $form?->title)" />
    <label class="grid gap-2">
        <span class="text-sm font-semibold text-slate-700">Form type <span class="text-rose-600">*</span></span>
        <select name="type" required class="form-control">
            <option value="">Select type</option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $form?->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type') <span class="text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2">
        <span class="text-sm font-semibold text-slate-700">Status <span class="text-rose-600">*</span></span>
        <select name="status" required class="form-control">
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $form?->status ?? 'draft') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <span class="text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
    </label>
    <x-field name="owner" label="Owner or responsible person" :value="old('owner', $form?->owner)" />
    <x-field name="reference" label="Reference number" :value="old('reference', $form?->reference)" />
    <x-field name="effective_at" label="Effective date" type="date" :value="old('effective_at', $form?->effective_at?->format('Y-m-d'))" />
    <x-field name="expires_at" label="Expiry date" type="date" :value="old('expires_at', $form?->expires_at?->format('Y-m-d'))" />
    <x-field name="review_due_at" label="Review due date" type="date" :value="old('review_due_at', $form?->review_due_at?->format('Y-m-d'))" />

    <div class="grid gap-6 lg:col-span-2">
        <x-textarea name="purpose" label="Purpose" rows="4" :value="old('purpose', $form?->purpose)" />
        <x-textarea name="data_subjects" label="Data subjects or affected people" rows="4" :value="old('data_subjects', $form?->data_subjects)" />
        <x-textarea name="legal_basis" label="Legal basis or authorising ground" rows="4" :value="old('legal_basis', $form?->legal_basis)" />
        <x-textarea name="authorisation_details" label="Consent / authorisation details" rows="5" :value="old('authorisation_details', $form?->authorisation_details)" />
        <x-textarea name="safeguards" label="Safeguards, controls, or transfer protections" rows="5" :value="old('safeguards', $form?->safeguards)" />
        <x-textarea name="notes" label="Notes and follow-up actions" rows="5" :value="old('notes', $form?->notes)" />
    </div>

    <div class="flex flex-wrap gap-3 lg:col-span-2">
        <button type="submit" class="button-primary">{{ $form ? 'Update form' : 'Save form' }}</button>
        <a href="{{ route('compliance.forms.index') }}" class="button-secondary">Cancel</a>
    </div>
</form>
@endsection
