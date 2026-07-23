<?php

namespace App\Services;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    /**
     * Create a Razorpay order.
     *
     * @param  int    $amountInPaise  Amount in smallest currency unit (paise)
     * @param  string $receipt        Unique receipt reference (booking ID)
     * @param  array  $notes          Optional metadata
     * @return \Razorpay\Api\Order
     */
    public function createOrder(int $amountInPaise, string $receipt, array $notes = []): \Razorpay\Api\Order
    {
        return $this->api->order->create([
            'amount'          => $amountInPaise,
            'currency'        => config('services.razorpay.currency', 'INR'),
            'receipt'         => $receipt,
            'payment_capture' => 1,   // auto-capture
            'notes'           => $notes,
        ]);
    }

    /**
     * Verify Razorpay payment signature.
     *
     * @throws SignatureVerificationError
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('[Razorpay] Signature verification failed', [
                'order_id'   => $orderId,
                'payment_id' => $paymentId,
                'error'      => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        try {
            $this->api->utility->verifyWebhookSignature(
                $payload,
                $signature,
                config('services.razorpay.webhook_secret')
            );
            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('[Razorpay] Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Fetch a payment by ID from Razorpay.
     */
    public function fetchPayment(string $paymentId): \Razorpay\Api\Payment
    {
        return $this->api->payment->fetch($paymentId);
    }
}
