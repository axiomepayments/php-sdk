<?php

namespace AxiomePayments\Model;

/**
 * Currency model representing a processing currency
 */
class Currency
{
    /**
     * @var string Currency code (e.g., 'USD', 'EUR')
     */
    public $short_code;

    /**
     * @var string Currency name (e.g., 'US Dollar')
     */
    public $name;

    /**
     * @var string Currency symbol (e.g., '$', '€')
     */
    public $symbol;

    /**
     * @var string|null Current USD exchange rate value
     */
    public $current_usd_value;

    /**
     * @var string|null Currency code this currency is processed via
     */
    public $processed_via_currency_code;

    /**
     * Create a new Currency instance from API response data
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $currency = new static();

        $currency->short_code = $data['short_code'] ?? null;
        $currency->name = $data['name'] ?? null;
        $currency->symbol = $data['symbol'] ?? null;
        $currency->current_usd_value = $data['current_usd_value'] ?? null;
        $currency->processed_via_currency_code = $data['processed_via_currency_code'] ?? null;

        return $currency;
    }

    /**
     * Convert the currency to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'short_code' => $this->short_code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'current_usd_value' => $this->current_usd_value,
            'processed_via_currency_code' => $this->processed_via_currency_code,
        ], function ($value) {
            return $value !== null;
        });
    }

    /**
     * Get the currency code
     *
     * @return string|null
     */
    public function getShortCode(): ?string
    {
        return $this->short_code;
    }

    /**
     * Get the currency name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the currency symbol
     *
     * @return string|null
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * Get the current USD exchange rate value
     *
     * @return string|null
     */
    public function getCurrentUsdValue(): ?string
    {
        return $this->current_usd_value;
    }

    /**
     * Get the currency code this currency is processed via
     *
     * @return string|null
     */
    public function getProcessedViaCurrencyCode(): ?string
    {
        return $this->processed_via_currency_code;
    }

    /**
     * Check if this currency is processed via another currency
     *
     * @return bool
     */
    public function isProcessedViaAnotherCurrency(): bool
    {
        return !empty($this->processed_via_currency_code);
    }
}
