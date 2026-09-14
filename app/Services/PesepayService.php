<?php

namespace App\Services;

use App\Models\MonthlyPayment;
use Codevirtus\Payments\Pesepay;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class PesepayService
{
    /** @return array<string, mixed> */
    public function initiate(
        MonthlyPayment $payment,
        string $returnUrl,
        string $resultUrl,
        string $paymentMethodCode,
        ?string $customerPhoneNumber = null,
    ): array
    {
        $integrationKey = config('services.pesepay.integration_key');
        $encryptionKey = config('services.pesepay.encryption_key');
        $endpoint = config('services.pesepay.make_payment_url');

        if (! $integrationKey || ! $encryptionKey || ! $endpoint) {
            throw new RuntimeException('Pesepay is not configured yet.');
        }

        if ($this->shouldUseSdk()) {
            return $this->initiateWithSdk($payment, $returnUrl, $resultUrl);
        }

        $body = [
            'amountDetails' => [
                'amount' => $payment->amount_cents / 100,
                'currencyCode' => $payment->currency,
            ],
            'merchantReference' => $payment->merchant_reference,
            'reasonForPayment' => 'Protego monthly DP1/DP2 download access',
            'resultUrl' => $resultUrl,
            'returnUrl' => $returnUrl,
            'paymentMethodCode' => $paymentMethodCode,
            'customer' => [
                'email' => $payment->user->email,
                'phoneNumber' => $customerPhoneNumber ?? '',
                'name' => $payment->user->name,
            ],
            'paymentMethodRequiredFields' => $customerPhoneNumber
                ? ['customerPhoneNumber' => $customerPhoneNumber]
                : [],
        ];

        try {
            $response = Http::withHeaders([
                'authorization' => $integrationKey,
                'content-type' => 'application/json',
            ])->post($endpoint, [
                'payload' => $this->encrypt($body, $encryptionKey),
            ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Could not connect to Pesepay.', previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException($this->rejectionMessage(
                'Pesepay rejected the payment request.',
                $response->json(),
                $response->body(),
            ));
        }

        $transaction = $this->transactionFromResponse($response->json(), $encryptionKey);
        if ($redirectUrl = $this->redirectUrlFrom($transaction)) {
            $transaction['redirectUrl'] = $redirectUrl;
        }

        return $transaction;
    }

    /** @return array<string, mixed> */
    public function checkStatus(string $referenceNumber): array
    {
        $integrationKey = config('services.pesepay.integration_key');
        $encryptionKey = config('services.pesepay.encryption_key');
        $endpoint = config('services.pesepay.check_payment_url');

        if (! $integrationKey || ! $encryptionKey || ! $endpoint) {
            throw new RuntimeException('Pesepay is not configured yet.');
        }

        if ($this->shouldUseSdk()) {
            return $this->checkStatusWithSdk($referenceNumber);
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $integrationKey,
                'content-type' => 'application/json',
            ])->get($endpoint, ['referenceNumber' => $referenceNumber]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Could not connect to Pesepay.', previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException($this->rejectionMessage(
                'Pesepay rejected the payment status request.',
                $response->json(),
                $response->body(),
            ));
        }

        $payload = $response->json('payload');

        if (! is_string($payload)) {
            throw new RuntimeException('Pesepay returned an invalid status response.');
        }

        return $this->decrypt($payload, $encryptionKey);
    }

    /** @param array<string, mixed> $data */
    public function encrypt(array $data, string $key): string
    {
        $encrypted = openssl_encrypt(
            json_encode($data, JSON_THROW_ON_ERROR),
            'AES-256-CBC',
            $key,
            0,
            substr($key, 0, 16),
        );

        if ($encrypted === false) {
            throw new RuntimeException('Could not encrypt the Pesepay payload.');
        }

        return $encrypted;
    }

    /** @return array<string, mixed> */
    public function decrypt(string $payload, string $key): array
    {
        $decrypted = openssl_decrypt($payload, 'AES-256-CBC', $key, 0, substr($key, 0, 16));

        if ($decrypted === false) {
            throw new RuntimeException('Could not decrypt the Pesepay response.');
        }

        $transaction = json_decode($decrypted, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($transaction)) {
            throw new RuntimeException('Pesepay returned an invalid transaction.');
        }

        return $transaction;
    }

    /** @return array<string, mixed> */
    private function initiateWithSdk(MonthlyPayment $payment, string $returnUrl, string $resultUrl): array
    {
        $pesepay = $this->sdk();
        $pesepay->returnUrl = $returnUrl;
        $pesepay->resultUrl = $resultUrl;

        try {
            $transaction = $pesepay->createTransaction(
                $payment->amount_cents / 100,
                $this->config('currency', $payment->currency),
                'Protego monthly DP1/DP2 download access',
                $payment->merchant_reference,
            );

            $response = $pesepay->initiateTransaction($transaction);
        } catch (Throwable $exception) {
            throw new RuntimeException('Could not start the Pesepay checkout.', previous: $exception);
        }

        if (! $response->success()) {
            throw new RuntimeException($response->message() ?: 'Pesepay could not start the payment. Please try again.');
        }

        $rawData = method_exists($response, 'rawData') ? $response->rawData() : [];
        $redirectUrl = $this->redirectUrlFrom($rawData) ?: $response->redirectUrl();

        if (! is_string($redirectUrl) || $redirectUrl === '' || $this->isLocalRedirectUrl($redirectUrl)) {
            throw new RuntimeException('Pesepay did not return a checkout page. Please confirm hosted checkout is enabled on your Pesepay merchant account.');
        }

        return array_merge($rawData, [
            'referenceNumber' => $response->referenceNumber() ?: $payment->merchant_reference,
            'pollUrl' => $response->pollUrl(),
            'redirectUrl' => $redirectUrl,
        ]);
    }

    /** @return array<string, mixed> */
    private function checkStatusWithSdk(string $referenceNumber): array
    {
        try {
            $response = $this->sdk()->checkPayment($referenceNumber);
        } catch (Throwable $exception) {
            throw new RuntimeException('Could not check the Pesepay payment status.', previous: $exception);
        }

        if (! $response->success()) {
            throw new RuntimeException($response->message() ?: 'Pesepay rejected the payment status request.');
        }

        $rawData = method_exists($response, 'rawData') ? $response->rawData() : [];

        return array_merge($rawData, [
            'referenceNumber' => $response->referenceNumber() ?: $referenceNumber,
            'pollUrl' => $response->pollUrl(),
            'transactionStatus' => $response->paid() ? 'SUCCESS' : ($rawData['transactionStatus'] ?? null),
        ]);
    }

    private function sdk(): Pesepay
    {
        return new Pesepay(
            $this->config('integration_key'),
            $this->config('encryption_key'),
            $this->booleanConfig('sandbox', false),
        );
    }

    private function shouldUseSdk(): bool
    {
        return class_exists(Pesepay::class) && $this->booleanConfig('use_sdk', true);
    }

    private function booleanConfig(string $key, bool $default): bool
    {
        return filter_var($this->config($key, $default), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return config('services.pesepay.'.$key, $default);
    }

    /** @param array<string, mixed>|null $response */
    private function transactionFromResponse(?array $response, string $encryptionKey): array
    {
        if ($response === null) {
            throw new RuntimeException('Pesepay returned an invalid response.');
        }

        if (isset($response['payload']) && is_string($response['payload'])) {
            return $this->decrypt($response['payload'], $encryptionKey);
        }

        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        if (isset($response['transaction']) && is_array($response['transaction'])) {
            return $response['transaction'];
        }

        return $response;
    }

    /** @param array<string, mixed> $transaction */
    private function redirectUrlFrom(array $transaction): ?string
    {
        $candidates = [
            $transaction['redirectUrl'] ?? null,
            $transaction['redirect_url'] ?? null,
            $transaction['checkoutUrl'] ?? null,
            $transaction['checkout_url'] ?? null,
            $transaction['data']['redirectUrl'] ?? null,
            $transaction['data']['redirect_url'] ?? null,
            $transaction['transaction']['redirectUrl'] ?? null,
            $transaction['transaction']['redirect_url'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isLocalRedirectUrl(string $redirectUrl): bool
    {
        $redirectHost = parse_url($redirectUrl, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return $redirectHost && $appHost && strcasecmp($redirectHost, $appHost) === 0;
    }

    /** @param array<string, mixed>|null $response */
    private function rejectionMessage(string $fallback, ?array $response, string $body): string
    {
        $message = $response['message']
            ?? $response['error']
            ?? $response['description']
            ?? $response['transactionStatusDescription']
            ?? null;

        if (is_string($message) && $message !== '') {
            return $fallback.' '.$message;
        }

        if ($body !== '') {
            return $fallback.' '.Str::limit($body, 240);
        }

        return $fallback;
    }
}
