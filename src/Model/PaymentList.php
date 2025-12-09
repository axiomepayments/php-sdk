<?php

namespace AxiomePayments\Model;

/**
 * PaymentList model representing a paginated list of payments
 */
class PaymentList
{
    /**
     * @var Payment[]
     */
    public $payments;

    /**
     * @var bool
     */
    public $has_more;

    /**
     * @var string|null
     */
    public $next_page_token;

    /**
     * @var int
     */
    public $count;

    /**
     * Create a new PaymentList instance from API response data
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $list = new static();
        
        $list->payments = [];
        if (isset($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $paymentData) {
                $list->payments[] = Payment::fromArray($paymentData);
            }
        }
        
        $list->has_more = $data['has_more'] ?? false;
        $list->next_page_token = $data['next_page_token'] ?? null;
        $list->count = $data['count'] ?? count($list->payments);

        return $list;
    }

    /**
     * Convert the payment list to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'payments' => array_map(function (Payment $payment) {
                return $payment->toArray();
            }, $this->payments),
            'has_more' => $this->has_more,
            'next_page_token' => $this->next_page_token,
            'count' => $this->count,
        ];
    }

    /**
     * Get the number of payments in this list
     *
     * @return int
     */
    public function getCount(): int
    {
        return count($this->payments);
    }

    /**
     * Check if there are more payments available
     *
     * @return bool
     */
    public function hasMore(): bool
    {
        return $this->has_more;
    }

    /**
     * Get the next page token for pagination
     *
     * @return string|null
     */
    public function getNextPageToken(): ?string
    {
        return $this->next_page_token;
    }

    public function getPayments(): ?array
    {
        return $this->payments;
    }
} 