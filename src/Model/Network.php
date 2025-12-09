<?php

namespace AxiomePayments\Model;

/**
 * Network model representing a settlement network (blockchain network)
 */
class Network
{
    /**
     * @var string Network short code (e.g., 'POL', 'ETH')
     */
    public $short_code;

    /**
     * @var string Network identifier (e.g., 'polygon', 'ethereum')
     */
    public $identifier;

    /**
     * @var string Network name (e.g., 'Polygon', 'Ethereum')
     */
    public $name;

    /**
     * @var string|null Token standard (e.g., 'ERC-20', 'BEP-20')
     */
    public $token_standard;

    /**
     * @var int|null Blockchain chain ID
     */
    public $chain_id;

    /**
     * @var string|null Block explorer URL
     */
    public $explorer_url;

    /**
     * @var string|null Full URL to network icon image
     */
    public $icon_url;

    /**
     * @var bool Whether this is a testnet or mainnet
     */
    public $is_testnet;

    /**
     * Create a new Network instance from API response data
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $network = new static();

        $network->short_code = $data['short_code'] ?? null;
        $network->identifier = $data['identifier'] ?? null;
        $network->name = $data['name'] ?? null;
        $network->token_standard = $data['token_standard'] ?? null;
        $network->chain_id = $data['chain_id'] ?? null;
        $network->explorer_url = $data['explorer_url'] ?? null;
        $network->icon_url = $data['icon_url'] ?? null;
        $network->is_testnet = $data['is_testnet'] ?? false;

        return $network;
    }

    /**
     * Convert the network to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'short_code' => $this->short_code,
            'identifier' => $this->identifier,
            'name' => $this->name,
            'token_standard' => $this->token_standard,
            'chain_id' => $this->chain_id,
            'explorer_url' => $this->explorer_url,
            'icon_url' => $this->icon_url,
            'is_testnet' => $this->is_testnet,
        ], function ($value) {
            return $value !== null;
        });
    }

    /**
     * Get the network short code
     *
     * @return string|null
     */
    public function getShortCode(): ?string
    {
        return $this->short_code;
    }

    /**
     * Get the network identifier
     *
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * Get the network name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the token standard
     *
     * @return string|null
     */
    public function getTokenStandard(): ?string
    {
        return $this->token_standard;
    }

    /**
     * Get the blockchain chain ID
     *
     * @return int|null
     */
    public function getChainId(): ?int
    {
        return $this->chain_id;
    }

    /**
     * Get the block explorer URL
     *
     * @return string|null
     */
    public function getExplorerUrl(): ?string
    {
        return $this->explorer_url;
    }

    /**
     * Get the network icon URL
     *
     * @return string|null
     */
    public function getIconUrl(): ?string
    {
        return $this->icon_url;
    }

    /**
     * Check if this is a testnet
     *
     * @return bool
     */
    public function isTestnet(): bool
    {
        return $this->is_testnet;
    }
}
