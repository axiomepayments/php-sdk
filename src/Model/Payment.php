<?php

namespace AxiomePayments\Model;

/**
 * Payment model representing a payment transaction or payment link
 */
class Payment
{
    /**
     * @var string|null Payment link ID
     */
    public $id;

    /**
     * @var string|null Transaction ID (only present after payment is initiated)
     */
    public $transaction_id;

    /**
     * @var string|null Payment title
     */
    public $title;

    /**
     * @var string|null Payment description
     */
    public $description;

    /**
     * @var string|null Payment session reference ID
     */
    public $reference_id;

    /**
     * @var string|null Payment link ID (same as $id in create response)
     */
    public $payment_link_id;

    /**
     * @var string|null Flow type (e.g., 'nft')
     */
    public $flow_type;

    /**
     * @var string|null Payment URL where customer can complete payment
     */
    public $payment_url;

    /**
     * @var string|null Embeddable payment URL with transparent background for iframe embedding
     */
    public $embed_url;

    /**
     * @var float|null Amount in fiat currency
     */
    public $amount;

    /**
     * @var float|null Base fiat amount (before fees)
     */
    public $fiat_base_amount;

    /**
     * @var float|null Total fiat amount (including fees)
     */
    public $fiat_total_amount;

    /**
     * @var string|null Fiat currency code (e.g., 'USD', 'EUR')
     */
    public $currency;

    /**
     * @var string|null Fiat currency code (same as $currency)
     */
    public $fiat_currency;

    /**
     * @var string|null Commodity type (e.g., 'USDT')
     */
    public $commodity;

    /**
     * @var float|null Amount of commodity to be purchased
     */
    public $commodity_amount;

    /**
     * @var float|null Amount of commodity settled to merchant wallets
     */
    public $settled_amount;

    /**
     * @var string|null Payment status (pending, attempting, processing, completed, failed, expired, cancelled)
     */
    public $status;

    /**
     * @var string|null Reason for payment failure
     */
    public $fail_reason;

    /**
     * @var string|null ISO 8601 timestamp when payment was created
     */
    public $created_at;

    /**
     * @var string|null ISO 8601 timestamp when payment was last updated
     */
    public $updated_at;

    /**
     * @var string|null ISO 8601 timestamp when payment was completed
     */
    public $paid_at;

    /**
     * @var string|null ISO 8601 timestamp when payment expires
     */
    public $expires_at;

    /**
     * @var array|null Custom fields
     */
    public $custom_fields;

    /**
     * @var float|null Customer commission percentage
     */
    public $customer_commission_percentage;

    /**
     * @var bool|null Whether payment link can be used multiple times
     */
    public $multiple_use;

    /**
     * @var array|null Customer details
     */
    public $customer_details;

    /**
     * @var array|null Optional metadata for identifying customer or payment session
     *                 Returned in webhooks and API responses
     */
    public $metadata;

    /**
     * @var array|null Payment method information with keys:
     *                 - card_id: Card identifier
     *                 - card_brand: Card brand ('VISA', 'MASTERCARD', 'AMEX', 'JCB', 'DISCOVER')
     *                 - payment_type: Payment type (e.g., '3ds_v2', 'non_3ds')
     *                 - processed_through: Payment processor ('safecharge', 'ap_safecharge', 'gp_safecharge', 'worldpay')
     */
    public $payment_method;

    /**
     * @var string|null Blockchain transaction hash for completed crypto transfers
     */
    public $blockchain_tx_hash;

    /**
     * Create a new Payment instance from API response data
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $payment = new static();

        $payment->id = $data['id'] ?? null;
        $payment->transaction_id = $data['transaction_id'] ?? null;
        $payment->title = $data['title'] ?? null;
        $payment->description = $data['description'] ?? null;
        $payment->reference_id = $data['reference_id'] ?? null;
        $payment->payment_link_id = $data['payment_link_id'] ?? null;
        $payment->flow_type = $data['flow_type'] ?? null;
        $payment->payment_url = $data['payment_url'] ?? null;
        $payment->embed_url = $data['embed_url'] ?? null;
        $payment->amount = isset($data['amount']) ? (float) $data['amount'] : null;
        $payment->fiat_base_amount = isset($data['fiat_base_amount']) ? (float) $data['fiat_base_amount'] : null;
        $payment->fiat_total_amount = isset($data['fiat_total_amount']) ? (float) $data['fiat_total_amount'] : null;
        $payment->currency = $data['currency'] ?? null;
        $payment->fiat_currency = $data['fiat_currency'] ?? null;
        $payment->commodity = $data['commodity'] ?? null;
        $payment->commodity_amount = isset($data['commodity_amount']) ? (float) $data['commodity_amount'] : null;
        $payment->settled_amount = isset($data['settled_amount']) ? (float) $data['settled_amount'] : null;
        $payment->status = $data['status'] ?? null;
        $payment->fail_reason = $data['fail_reason'] ?? null;
        $payment->created_at = $data['created_at'] ?? null;
        $payment->updated_at = $data['updated_at'] ?? null;
        $payment->paid_at = $data['paid_at'] ?? null;
        $payment->expires_at = $data['expires_at'] ?? null;
        $payment->custom_fields = $data['custom_fields'] ?? null;
        $payment->customer_commission_percentage = isset($data['customer_commission_percentage']) ? (float) $data['customer_commission_percentage'] : null;
        $payment->multiple_use = $data['multiple_use'] ?? null;
        $payment->customer_details = $data['customer_details'] ?? null;
        $payment->metadata = $data['metadata'] ?? null;
        $payment->payment_method = $data['payment_method'] ?? null;
        $payment->blockchain_tx_hash = $data['blockchain_tx_hash'] ?? null;

        return $payment;
    }

    /**
     * Convert the payment to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'title' => $this->title,
            'description' => $this->description,
            'reference_id' => $this->reference_id,
            'payment_link_id' => $this->payment_link_id,
            'flow_type' => $this->flow_type,
            'payment_url' => $this->payment_url,
            'embed_url' => $this->embed_url,
            'amount' => $this->amount,
            'fiat_base_amount' => $this->fiat_base_amount,
            'fiat_total_amount' => $this->fiat_total_amount,
            'currency' => $this->currency,
            'fiat_currency' => $this->fiat_currency,
            'commodity' => $this->commodity,
            'commodity_amount' => $this->commodity_amount,
            'settled_amount' => $this->settled_amount,
            'status' => $this->status,
            'fail_reason' => $this->fail_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'paid_at' => $this->paid_at,
            'expires_at' => $this->expires_at,
            'custom_fields' => $this->custom_fields,
            'customer_commission_percentage' => $this->customer_commission_percentage,
            'multiple_use' => $this->multiple_use,
            'customer_details' => $this->customer_details,
            'metadata' => $this->metadata,
            'payment_method' => $this->payment_method,
            'blockchain_tx_hash' => $this->blockchain_tx_hash,
        ], function ($value) {
            return $value !== null;
        });
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTransactionId(): ?string
    {
        return $this->transaction_id;
    }

    public function getReferenceId(): ?string
    {
        return $this->reference_id;
    }

    public function getPaymentLinkId(): ?string
    {
        return $this->payment_link_id;
    }

    public function getFlowType(): ?string
    {
        return $this->flow_type;
    }

    public function getPaymentUrl(): ?string
    {
        return $this->payment_url;
    }

    /**
     * Get the embeddable payment URL with transparent background for iframe embedding
     *
     * @return string|null
     */
    public function getEmbedUrl(): ?string
    {
        return $this->embed_url;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getFiatBaseAmount(): ?float
    {
        return $this->fiat_base_amount;
    }

    public function getFiatTotalAmount(): ?float
    {
        return $this->fiat_total_amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency ?? $this->fiat_currency;
    }

    public function getFiatCurrency(): ?string
    {
        return $this->fiat_currency ?? $this->currency;
    }

    public function getCommodity(): ?string
    {
        return $this->commodity;
    }

    public function getCommodityAmount(): ?float
    {
        return $this->commodity_amount;
    }

    public function getSettledAmount(): ?float
    {
        return $this->settled_amount;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getFailReason(): ?string
    {
        return $this->fail_reason;
    }

    public function getCustomerDetails(): ?array
    {
        return $this->customer_details;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getPaymentMethod(): ?array
    {
        return $this->payment_method;
    }

    /**
     * Get the blockchain transaction hash
     *
     * @return string|null
     */
    public function getBlockchainTxHash(): ?string
    {
        return $this->blockchain_tx_hash;
    }

    /**
     * Get the card ID from payment method
     *
     * @return string|null
     */
    public function getCardId(): ?string
    {
        return $this->payment_method['card_id'] ?? null;
    }

    /**
     * Get the card brand from payment method ('VISA', 'MASTERCARD', 'AMEX', 'JCB', 'DISCOVER')
     *
     * @return string|null
     */
    public function getCardBrand(): ?string
    {
        return $this->payment_method['card_brand'] ?? null;
    }

    /**
     * Get the payment type from payment method (e.g., '3ds_v2', 'non_3ds')
     *
     * @return string|null
     */
    public function getPaymentType(): ?string
    {
        return $this->payment_method['payment_type'] ?? null;
    }

    /**
     * Get the payment processor from payment method ('safecharge', 'ap_safecharge', 'gp_safecharge', 'worldpay')
     *
     * @return string|null
     */
    public function getProcessedThrough(): ?string
    {
        return $this->payment_method['processed_through'] ?? null;
    }

    /**
     * Check if the payment is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the payment is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment is attempting (payment started)
     *
     * @return bool
     */
    public function isAttempting(): bool
    {
        return $this->status === 'attempting';
    }

    /**
     * Check if the payment is processing (transfer started)
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the payment has failed
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the payment has expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if the payment was cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
} 