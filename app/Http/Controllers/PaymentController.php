<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Person;
use App\Models\Pledge;
use App\Models\User;
use App\Services\PaystackService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaystackService $paystack;
    protected PushNotificationService $pushNotification;

    public function __construct(PaystackService $paystack, PushNotificationService $pushNotification)
    {
        $this->paystack = $paystack;
        $this->pushNotification = $pushNotification;
    }

    /**
     * Initiate Paystack transaction from giving form.
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['required', 'string', 'in:offering,tithe,donation,pledge,special_project'],
            'fund_id' => ['nullable', 'exists:funds,id'],
            'pledge_id' => ['nullable', 'exists:pledges,id'],
            'email' => ['required', 'email'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        $personId = $user?->person_id;
        if (!$personId && !empty($validated['email'])) {
            $person = Person::where('email', $validated['email'])->first();
            $personId = $person?->id;
        }

        $fundId = $validated['fund_id'] ?? Fund::where('is_active', true)->value('id') ?? 1;

        $reference = 'CHMS_' . strtoupper(uniqid()) . '_' . time();

        $metadata = [
            'payment_type' => $validated['payment_type'],
            'fund_id' => $fundId,
            'pledge_id' => $validated['pledge_id'] ?? null,
            'person_id' => $personId,
            'user_id' => $user?->id,
            'donor_name' => $validated['donor_name'] ?? ($user?->name ?? 'Guest Donor'),
            'notes' => $validated['notes'] ?? '',
            'is_anonymous' => !empty($validated['is_anonymous']),
        ];

        // Store initiated transaction state in session for quick retrieval on return
        session()->put('pending_payment_' . $reference, [
            'amount' => (float) $validated['amount'],
            'email' => $validated['email'],
            'metadata' => $metadata,
        ]);

        $res = $this->paystack->initializeTransaction([
            'email' => $validated['email'],
            'amount' => (float) $validated['amount'],
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'metadata' => $metadata,
        ]);

        if ($res && !empty($res['data']['authorization_url'])) {
            return redirect($res['data']['authorization_url']);
        }

        return back()->with('error', 'Unable to connect to Paystack payment gateway. Please verify your internet connection or credentials.');
    }

    /**
     * Handle Paystack callback on transaction completion.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference) {
            return redirect()->route('giving.pay')->with('error', 'Payment reference was not provided.');
        }

        // Check if already processed
        $existing = Contribution::where('reference', $reference)->first();
        if ($existing) {
            return redirect()->route('payment.receipt', $reference);
        }

        $verification = $this->paystack->verifyTransaction($reference);

        if (!$verification || empty($verification['data']) || $verification['data']['status'] !== 'success') {
            Log::warning('Paystack verification failed on callback', ['reference' => $reference, 'response' => $verification]);
            return redirect()->route('giving.pay')->with('error', 'Payment verification was not successful. If debited, please contact the church office.');
        }

        $txData = $verification['data'];
        $amount = (float) ($txData['amount'] / 100);
        $metadata = $txData['metadata'] ?? session()->get('pending_payment_' . $reference . '.metadata', []);

        $fundId = $metadata['fund_id'] ?? Fund::where('is_active', true)->value('id') ?? 1;
        $personId = $metadata['person_id'] ?? auth()->user()?->person_id;
        $pledgeId = $metadata['pledge_id'] ?? null;
        $paymentType = $metadata['payment_type'] ?? 'offering';
        $notes = $metadata['notes'] ?? '';
        $isAnonymous = !empty($metadata['is_anonymous']);

        $contribution = Contribution::create([
            'person_id' => $personId,
            'fund_id' => $fundId,
            'pledge_id' => $pledgeId,
            'amount' => $amount,
            'contributed_on' => now()->toDateString(),
            'method' => 'paystack',
            'reference' => $reference,
            'is_anonymous' => $isAnonymous,
            'note' => ucfirst(str_replace('_', ' ', $paymentType)) . ($notes ? ': ' . $notes : ''),
            'recorded_by' => auth()->id(),
        ]);

        // Clear session pending payment
        session()->forget('pending_payment_' . $reference);

        // Send Push Notification if user is logged in
        if (auth()->check()) {
            $currency = config('paystack.currency', 'GHS');
            $this->pushNotification->sendToUser(
                auth()->user(),
                'Payment Received',
                "Thank you! Your {$paymentType} of {$currency} " . number_format($amount, 2) . ' was received successfully.',
                route('payment.receipt', $reference)
            );
        }

        return redirect()->route('payment.receipt', $reference)->with('success', 'Thank you! Your payment was received successfully.');
    }

    /**
     * Display printable receipt for Paystack payment.
     */
    public function receipt(string $reference)
    {
        $contribution = Contribution::with(['fund', 'person', 'pledge'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('giving.receipt', [
            'contribution' => $contribution,
            'currency' => config('paystack.currency', 'GHS'),
        ]);
    }

    /**
     * Simulated / Mock Paystack Checkout (for local development or testing).
     */
    public function mockCheckout(Request $request)
    {
        $reference = $request->query('reference');
        $pending = session()->get('pending_payment_' . $reference, [
            'amount' => 100.00,
            'email' => 'donor@example.com',
            'metadata' => [
                'payment_type' => 'offering',
                'donor_name' => 'Church Member',
            ],
        ]);

        return view('giving.mock-paystack', [
            'reference' => $reference,
            'pending' => $pending,
            'currency' => config('paystack.currency', 'GHS'),
        ]);
    }

    /**
     * Paystack Webhook Handler.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature', '');
        $payload = $request->getContent();

        if (!$this->paystack->validateWebhookSignature($signature, $payload)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        if (($event['event'] ?? '') === 'charge.success') {
            $data = $event['data'] ?? [];
            $reference = $data['reference'] ?? null;

            if ($reference && !Contribution::where('reference', $reference)->exists()) {
                $amount = (float) ($data['amount'] / 100);
                $metadata = $data['metadata'] ?? [];

                Contribution::create([
                    'person_id' => $metadata['person_id'] ?? null,
                    'fund_id' => $metadata['fund_id'] ?? 1,
                    'pledge_id' => $metadata['pledge_id'] ?? null,
                    'amount' => $amount,
                    'contributed_on' => now()->toDateString(),
                    'method' => 'paystack',
                    'reference' => $reference,
                    'is_anonymous' => !empty($metadata['is_anonymous']),
                    'note' => 'Paystack Webhook: ' . ($metadata['payment_type'] ?? 'giving'),
                    'recorded_by' => null,
                ]);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
