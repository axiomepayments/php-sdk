<?php

namespace AxiomePayments;

use AxiomePayments\Service\PaymentService;
use AxiomePayments\Service\CurrencyService;
use AxiomePayments\Service\NetworkService;
use AxiomePayments\Service\CommodityService;
use AxiomePayments\Http\Client;

/**
 * AxiomePayments PHP SDK
 *
 * @property PaymentService $payments
 * @property CurrencyService $currencies
 * @property NetworkService $networks
 * @property CommodityService $commodities
 */
class AxiomePayments
{
    public const API_VERSION = 'v1.0';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var PaymentService
     */
    private $payments;

    /**
     * @var CurrencyService
     */
    private $currencies;

    /**
     * @var NetworkService
     */
    private $networks;

    /**
     * @var CommodityService
     */
    private $commodities;

    /**
     * @var array
     */
    private $config;

    /**
     * Create a new AxiomePayments instance
     *
     * @param array $config Configuration array
     * @throws Exception\AxiomePaymentsException
     */
    public function __construct(array $config = [])
    {
        $this->validateConfig($config);
        $this->config = $this->mergeDefaultConfig($config);
        $this->client = new Client($this->config);
        $this->initializeServices();
    }

    /**
     * Get the HTTP client instance
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Magic getter for services
     *
     * @param string $name
     * @return mixed
     * @throws Exception\AxiomePaymentsException
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'payments':
                return $this->payments;
            case 'currencies':
                return $this->currencies;
            case 'networks':
                return $this->networks;
            case 'commodities':
                return $this->commodities;
            default:
                throw new Exception\AxiomePaymentsException("Unknown service: {$name}");
        }
    }

    /**
     * Validate the configuration
     *
     * @param array $config
     * @throws Exception\AxiomePaymentsException
     */
    private function validateConfig(array $config): void
    {
        $required = ['api_key', 'api_secret'];

        foreach ($required as $key) {
            if (empty($config[$key])) {
                throw new Exception\AxiomePaymentsException("Missing required config: {$key}");
            }
        }

        if (isset($config['environment']) && !in_array($config['environment'], ['sandbox', 'production'])) {
            throw new Exception\AxiomePaymentsException("Invalid environment. Must be 'sandbox' or 'production'");
        }
    }

    /**
     * Merge with default configuration
     *
     * @param array $config
     * @return array
     */
    private function mergeDefaultConfig(array $config): array
    {
        // Check for environment variables first
        $envConfig = [
            'api_key' => getenv('AXIOMEPAYMENTS_API_KEY'),
            'api_secret' => getenv('AXIOMEPAYMENTS_API_SECRET'),
            'environment' => getenv('AXIOMEPAYMENTS_ENVIRONMENT'),
            'base_url' => getenv('AXIOMEPAYMENTS_API_URL'),
        ];

        // Remove null values from env config
        $envConfig = array_filter($envConfig, function ($value) {
            return $value !== false && $value !== null;
        });

        $defaults = [
            'environment' => 'production',
            'timeout' => 30,
            'connect_timeout' => 10,
            'user_agent' => 'AxiomePayments-PHP-SDK/1.1.0',
            'base_url' => null,
        ];

        // Merge in order of precedence: defaults -> env -> explicit config
        $merged = array_merge($defaults, $envConfig, $config);

        // Set base URL based on environment if not explicitly provided
        if (!$merged['base_url']) {
            $merged['base_url'] = $merged['environment'] === 'sandbox'
                ? 'https://sandbox-api.axiomepayments.com/' . self::API_VERSION
                : 'https://api.axiomepayments.com/' . self::API_VERSION;
        }

        // Ensure base_url doesn't end with a slash and includes API version
        $merged['base_url'] = rtrim($merged['base_url'], '/');
        if (!str_ends_with($merged['base_url'], self::API_VERSION)) {
            $merged['base_url'] .= '/' . self::API_VERSION;
        }

        return $merged;
    }

    /**
     * Initialize service instances
     */
    private function initializeServices(): void
    {
        $this->payments = new PaymentService($this->client);
        $this->currencies = new CurrencyService($this->client);
        $this->networks = new NetworkService($this->client);
        $this->commodities = new CommodityService($this->client);
    }
} 