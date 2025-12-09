<?php

namespace AxiomePayments;

use AxiomePayments\Exception\AxiomePaymentsException;

/**
 * Webhook utility class for verifying webhook signatures
 */
class Webhook
{
    /**
     * @var string
     */
    private $secret;

    /**
     * Create a new Webhook instance
     *
     * @param string $secret Webhook secret for signature verification
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload Raw webhook payload
     * @param string $signature Signature from X-Webhook-Signature header
     * @return bool
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        if (empty($this->secret)) {
            throw new AxiomePaymentsException('Webhook secret is required for signature verification');
        }

        if (empty($signature)) {
            return false;
        }

        // Remove 'sha256=' prefix if present
        $signature = str_replace('sha256=', '', $signature);

        // Compute expected signature
        $expectedSignature = hash_hmac('sha256', $payload, $this->secret);

        // Debug logging
        error_log('AxiomePayments SDK Signature Debug:');
        error_log('Signature received (after prefix removal): ' . $signature);
        error_log('Signature computed: ' . $expectedSignature);
        error_log('Match: ' . ($expectedSignature === $signature ? 'YES' : 'NO'));

        // Use timing-safe comparison
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Parse webhook payload
     *
     * @param string $payload Raw webhook payload
     * @return array
     * @throws AxiomePaymentsException
     */
    public function parsePayload(string $payload): array
    {
        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AxiomePaymentsException('Invalid JSON in webhook payload');
        }

        return $data;
    }

    /**
     * Verify and parse webhook
     *
     * @param string $payload Raw webhook payload
     * @param string $signature Signature from X-Webhook-Signature header
     * @return array
     * @throws AxiomePaymentsException
     */
    public function verifyAndParse(string $payload, string $signature): array
    {
        if (!$this->verifySignature($payload, $signature)) {
            throw new AxiomePaymentsException('Invalid webhook signature');
        }

        return $this->parsePayload($payload);
    }

    /**
     * Get webhook event type from parsed payload
     *
     * @param array $payload Parsed webhook payload
     * @return string|null
     */
    public function getEventType(array $payload): ?string
    {
        return $payload['event'] ?? null;
    }

    /**
     * Get webhook event data from parsed payload
     *
     * @param array $payload Parsed webhook payload
     * @return array|null
     */
    public function getEventData(array $payload): ?array
    {
        return $payload['data'] ?? null;
    }

    /**
     * Check if webhook event is a payment intent creation
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentCreated(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.created';
    }

    /**
     * Check if webhook event is a payment intent success
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentSucceeded(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.succeeded';
    }

     /**
     * Check if webhook event is a payment intent attempting
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentAttempting(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.attempting';
    }

    /**
     * Check if webhook event is a payment intent failure
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentFailed(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.failed';
    }

    /**
     * Check if webhook event is a payment intent cancellation
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentCancelled(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.cancelled';
    }

    /**
     * Check if webhook event is a payment intent expiration
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentExpired(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.expired';
    }

    /**
     * Check if webhook event is a payment intent expiration
     *
     * @param array $payload Parsed webhook payload
     * @return bool
     */
    public function isPaymentIntentProcessing(array $payload): bool
    {
        return $this->getEventType($payload) === 'payment_intent.processing';
    }
} 