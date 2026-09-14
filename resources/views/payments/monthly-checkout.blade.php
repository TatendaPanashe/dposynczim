@extends('layouts.app')
@section('content')
<x-page-heading eyebrow="Monthly access" title="Unlock DP1 and DP2 downloads" description="One payment covers your workspace for the current calendar month." />
<div class="surface grid gap-6 p-6 lg:p-8">
    <div class="grid gap-4 border-b border-slate-200 pb-6 md:grid-cols-[1fr_0.35fr] md:items-start">
        <div>
            <p class="text-sm leading-6 text-slate-600">DP1 and DP2 official PDF downloads require a monthly access payment. Once paid, your workspace can download DP1 and DP2 forms for the rest of this month without paying again.</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Payments are processed securely through Pesepay. Protego does not collect or store card or wallet credentials.</p>
        </div>
        <div class="border border-slate-200 bg-slate-50 p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Monthly access</p>
            <p class="mt-2 text-3xl font-bold">$1.99</p>
            <p class="mt-1 text-xs text-slate-500">Paid once per month</p>
        </div>
    </div>

    @error('payment')
        <div class="border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">{{ $message }}</div>
    @enderror

    @if($hasPesepayConfig)
        <form method="POST" action="{{ route('compliance.payments.initiate') }}" class="grid gap-5">
            @csrf
            <input type="hidden" name="document_type" value="{{ $documentType }}">
            @if($documentId !== null)
                <input type="hidden" name="document_id" value="{{ $documentId }}">
            @endif
            <div class="grid gap-5 md:grid-cols-2">
                <label class="grid gap-2">
                    <span class="text-sm font-semibold text-slate-700">Payment method <span class="text-cyan-700">*</span></span>
                    <select name="payment_method_code" required class="form-control">
                        <option value="" disabled @selected(old('payment_method_code') === null)>Select payment method</option>
                        <option value="PZW211" @selected(old('payment_method_code') === 'PZW211')>EcoCash USD</option>
                        <option value="PZW212" @selected(old('payment_method_code') === 'PZW212')>Innbucks</option>
                    </select>
                    @error('payment_method_code')<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-semibold text-slate-700">Customer phone number</span>
                    <input name="customer_phone_number" value="{{ old('customer_phone_number') }}" class="form-control" placeholder="Required for EcoCash">
                    @error('customer_phone_number')<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
                </label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="button-primary">Pay with Pesepay</button>
            </div>
        </form>
    @else
        <div class="border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">Pesepay is not configured yet. Add the Pesepay integration key, encryption key, payment method code, and payment URL to the environment before live payments can start.</div>
    @endif
</div>
@endsection
