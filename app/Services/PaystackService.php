<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;
    protected string $currency;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key', env('PAYSTACK_SECRET_KEY', ''));
        $this->publicKey = config('paystack.public_key', env('PAYSTACK_PUBLIC_KEY', ''));
        $this->baseUrl = rtrim(config('paystack.payment_url', 'https://api.paystack.co'), '/');
        $this->currency = config('paystack.currency', 'GHS');
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Initialize transaction with Paystack.
     */
    public function initializeTransaction(array $params): ?array
    {
        $amountInSubunit = (int) round(((float) $params['amount']) * 100);

        $payload = [
            'email' => $params['email'],
            'amount' => $amountInSubunit,
            'currency' => $params['currency'] ?? $this->currency,
            'reference' => $params['reference'] ?? ('CHMS_' . uniqid() . '_' . time()),
            'callback_url' => $params['callback_url'],
            'metadata' => $params['metadata'] ?? [],
        ];

        // If keys are placeholder or test without connectivity, provide a mock checkout fallback URL for seamless testing
        if (str_starts_with($this->secretKey, 'sk_test_sample') || empty($this->secretKey)) {
            Log::info('Paystack initialized in mock mode', $payload);
            return [
                'status' => true,
                'message' => 'Authorization URL created (mock mode)',
                'data' => [
                    'authorization_url' => route('payment.mock.checkout', ['reference' => $payload['reference']]),
                    'access_code' => 'MOCK_' . uniqid(),
                    'reference' => $payload['reference'],
                ],
            ];
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(15)
                ->post("{$this->baseUrl}/transaction/initialize", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Paystack transaction initialization failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Paystack initialization exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify transaction by reference.
     */
    public function verifyTransaction(string $reference): ?array
    {
        // Mock verification for local test keys
        if (str_starts_with($this->secretKey, 'sk_test_sample') || empty($this->secretKey)) {
            Log::info("Paystack transaction {$reference} verified in mock mode");
            return [
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => $reference,
                    'gateway_response' => 'Successful (Mock)',
                ],
            ];
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(15)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Paystack verification failed for ref {$reference}", [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error("Paystack verification exception for ref {$reference}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate incoming webhook signature.
     */
    public function validateWebhookSignature(string $signature, string $payload): bool
    {
        if (empty($this->secretKey)) {
            return false;
        }

        $calculated = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($calculated, $signature);
    }
}
