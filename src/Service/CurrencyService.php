<?php

namespace AxiomePayments\Service;

use AxiomePayments\Http\Client;
use AxiomePayments\Model\Currency;
use AxiomePayments\Exception\AxiomePaymentsException;

/**
 * Currency service for handling currency operations
 */
class CurrencyService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Create a new CurrencyService instance
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all active processing currencies
     *
     * @return Currency[]
     * @throws AxiomePaymentsException
     */
    public function all(): array
    {
        $response = $this->client->get('/currencies');

        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return array_map(function ($currencyData) {
            return Currency::fromArray($currencyData);
        }, $response['data']);
    }

    /**
     * Get all active processing currencies as an array
     *
     * @return array
     * @throws AxiomePaymentsException
     */
    public function allAsArray(): array
    {
        $currencies = $this->all();

        return array_map(function (Currency $currency) {
            return $currency->toArray();
        }, $currencies);
    }
}
