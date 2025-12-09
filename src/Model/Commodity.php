<?php

namespace AxiomePayments\Model;

/**
 * Commodity model representing a settlement commodity (cryptocurrency)
 */
class Commodity
{
    /**
     * @var string Commodity short code (e.g., 'USDT', 'USDC', 'ETH')
     */
    public $short_code;

    /**
     * @var string Commodity name (e.g., 'Tether USD', 'USD Coin')
     */
    public $name;

    /**
     * @var string|null Full URL to commodity icon image
     */
    public $icon_url;

    /**
     * @var string|null Current USD value of the commodity
     */
    public $current_usd_value;

    /**
     * @var string|null Smart contract address on the blockchain
     */
    public $contract_address;

    /**
     * @var int|null Number of decimal places for the commodity
     */
    public $decimals;

    /**
     * @var string|null Network short code this commodity operates on
     */
    public $network_short_code;

    /**
     * Create a new Commodity instance from API response data
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $commodity = new static();

        $commodity->short_code = $data['short_code'] ?? null;
        $commodity->name = $data['name'] ?? null;
        $commodity->icon_url = $data['icon_url'] ?? null;
        $commodity->current_usd_value = $data['current_usd_value'] ?? null;
        $commodity->contract_address = $data['contract_address'] ?? null;
        $commodity->decimals = $data['decimals'] ?? null;
        $commodity->network_short_code = $data['network_short_code'] ?? null;

        return $commodity;
    }

    /**
     * Convert the commodity to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'short_code' => $this->short_code,
            'name' => $this->name,
            'icon_url' => $this->icon_url,
            'current_usd_value' => $this->current_usd_value,
            'contract_address' => $this->contract_address,
            'decimals' => $this->decimals,
            'network_short_code' => $this->network_short_code,
        ], function ($value) {
            return $value !== null;
        });
    }

    /**
     * Get the commodity short code
     *
     * @return string|null
     */
    public function getShortCode(): ?string
    {
        return $this->short_code;
    }

    /**
     * Get the commodity name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the commodity icon URL
     *
     * @return string|null
     */
    public function getIconUrl(): ?string
    {
        return $this->icon_url;
    }

    /**
     * Get the current USD value
     *
     * @return string|null
     */
    public function getCurrentUsdValue(): ?string
    {
        return $this->current_usd_value;
    }

    /**
     * Get the smart contract address
     *
     * @return string|null
     */
    public function getContractAddress(): ?string
    {
        return $this->contract_address;
    }

    /**
     * Get the number of decimal places
     *
     * @return int|null
     */
    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    /**
     * Get the network short code this commodity operates on
     *
     * @return string|null
     */
    public function getNetworkShortCode(): ?string
    {
        return $this->network_short_code;
    }

    /**
     * Check if this is a native token (no contract address)
     *
     * @return bool
     */
    public function isNativeToken(): bool
    {
        return empty($this->contract_address);
    }
}
