<?php

namespace App\Http\Controllers;

use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\MonthlyPayment;
use App\Services\PesepayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function showDp1(Request $request, FormDp1 $formDp1): View|RedirectResponse
    {
        if ($this->hasAccess($request)) {
            return to_route('compliance.dp1.download', $formDp1);
        }

        return $this->checkoutView($request, 'dp1', $formDp1->getKey());
    }

    public function showDp2(Request $request, FormDp2 $formDp2): View|RedirectResponse
    {
        if ($this->hasAccess($request)) {
            return to_route('compliance.dp2.download', $formDp2);
        }

        return $this->checkoutView($request, 'dp2', $formDp2->getKey());
    }

    public function showRopa(Request $request): View|RedirectResponse
    {
        if ($this->hasAccess($request)) {
            return to_route('compliance.ropa.download');
        }

        return $this->checkoutView($request, 'ropa');
    }

    public function initiate(Request $request, PesepayService $pesepay): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'in:dp1,dp2,ropa'],
            'document_id' => ['nullable', 'required_unless:document_type,ropa', 'integer'],
            'payment_method_code' => ['required', 'in:PZW211,PZW212'],
            'customer_phone_number' => ['nullable', 'required_if:payment_method_code,PZW211', 'string', 'max:30'],
        ]);

        $organization = $request->user()->activeOrganization();

        abort_if($organization === null, Response::HTTP_FORBIDDEN);

        $payment = MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $request->user()->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'pending',
            'merchant_reference' => 'PROTEGO-'.now()->format('Ym').'-'.Str::upper(Str::random(8)),
            'metadata' => [
                'document_type' => $validated['document_type'],
                'document_id' => $validated['document_id'] ?? null,
                'payment_method_code' => $validated['payment_method_code'],
                'customer_phone_number' => $validated['customer_phone_number'] ?? null,
            ],
        ]);

        try {
            $transaction = $pesepay->initiate(
                $payment,
                route('compliance.payments.return', $payment),
                route('compliance.payments.result', $payment),
                $validated['payment_method_code'],
                $validated['customer_phone_number'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        $payment->update([
            'pesepay_reference' => $transaction['referenceNumber'] ?? null,
            'redirect_url' => $transaction['redirectUrl'] ?? null,
            'metadata' => array_merge($payment->metadata ?? [], ['pesepay' => $transaction]),
        ]);

        if (! isset($transaction['redirectUrl'])) {
            return $this->redirectToPaymentPage($payment)
                ->with('success', 'Your payment request was sent to Pesepay. Please complete any prompt from your payment provider, then try the download again.');
        }

        return redirect()->away($transaction['redirectUrl']);
    }

    public function result(Request $request, MonthlyPayment $payment, PesepayService $pesepay): Response
    {
        $this->recordPesepayResult($request, $payment, $pesepay);

        return response('OK');
    }

    public function returned(Request $request, MonthlyPayment $payment, PesepayService $pesepay): RedirectResponse
    {
        $this->recordPesepayResult($request, $payment, $pesepay);
        $this->pollPesepayStatus($payment->fresh(), $pesepay);

        if ($payment->fresh()->status === 'paid') {
            return $this->redirectToDocument($payment)->with('success', 'Payment received. Your monthly DP1 and DP2 downloads are unlocked.');
        }

        return $this->redirectToPaymentPage($payment)
            ->withErrors(['payment' => 'We could not confirm the payment yet. Please try again once Pesepay confirms it.']);
    }

    private function hasAccess(Request $request): bool
    {
        $organization = $request->user()->activeOrganization();

        if ($organization === null) {
            return false;
        }

        return MonthlyPayment::where('organization_id', $organization->id)
            ->where('month', now()->format('Y-m'))
            ->where('status', 'paid')
            ->exists();
    }

    private function checkoutView(Request $request, string $documentType, ?int $documentId = null): View
    {
        return view('payments.monthly-checkout', [
            'documentType' => $documentType,
            'documentId' => $documentId,
            'hasPesepayConfig' => filled(config('services.pesepay.integration_key'))
                && filled(config('services.pesepay.encryption_key')),
        ]);
    }

    private function recordPesepayResult(Request $request, MonthlyPayment $payment, PesepayService $pesepay): void
    {
        $transaction = $request->all();

        if ($payload = $request->input('payload')) {
            $transaction = $pesepay->decrypt($payload, config('services.pesepay.encryption_key'));
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], ['pesepay_result' => $transaction]),
        ]);

        $this->applyTransactionStatus($payment, $transaction);
    }

    private function pollPesepayStatus(MonthlyPayment $payment, PesepayService $pesepay): void
    {
        if ($payment->status !== 'pending' || ! $payment->pesepay_reference) {
            return;
        }

        try {
            $transaction = $pesepay->checkStatus($payment->pesepay_reference);
        } catch (RuntimeException) {
            return;
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], ['pesepay_status_check' => $transaction]),
        ]);

        $this->applyTransactionStatus($payment, $transaction);
    }

    /** @param array<string, mixed> $transaction */
    private function applyTransactionStatus(MonthlyPayment $payment, array $transaction): void
    {
        $status = Str::upper((string) ($transaction['transactionStatus'] ?? $transaction['status'] ?? ''));
        $result = Str::upper((string) ($transaction['result'] ?? ''));

        if (
            in_array($status, ['SUCCESS', 'SUCCESSFUL', 'PAID', 'COMPLETED', 'SETTLED'], true)
            || ($result === 'SUCCESS' && $status === 'SETTLED')
        ) {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);

            return;
        }

        if (in_array($status, ['FAILED', 'CANCELLED', 'CANCELED', 'DECLINED'], true)) {
            $payment->update(['status' => 'failed']);
        }
    }

    private function redirectToDocument(MonthlyPayment $payment): RedirectResponse
    {
        $metadata = $payment->metadata ?? [];

        if (($metadata['document_type'] ?? null) === 'dp1') {
            return to_route('compliance.dp1.download', $metadata['document_id']);
        }

        if (($metadata['document_type'] ?? null) === 'ropa') {
            return to_route('compliance.ropa.download');
        }

        return to_route('compliance.dp2.download', $metadata['document_id']);
    }

    private function redirectToPaymentPage(MonthlyPayment $payment): RedirectResponse
    {
        $metadata = $payment->metadata ?? [];

        if (($metadata['document_type'] ?? null) === 'dp1') {
            return to_route('compliance.payments.dp1', $metadata['document_id']);
        }

        if (($metadata['document_type'] ?? null) === 'ropa') {
            return to_route('compliance.payments.ropa');
        }

        return to_route('compliance.payments.dp2', $metadata['document_id']);
    }
}
