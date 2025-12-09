<?php

namespace AxiomePayments\Service;

use AxiomePayments\Http\Client;
use AxiomePayments\Model\Network;
use AxiomePayments\Exception\AxiomePaymentsException;

/**
 * Network service for handling settlement network operations
 */
class NetworkService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Create a new NetworkService instance
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all active settlement networks
     *
     * @return Network[]
     * @throws AxiomePaymentsException
     */
    public function all(): array
    {
        $response = $this->client->get('/networks');

        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return array_map(function ($networkData) {
            return Network::fromArray($networkData);
        }, $response['data']);
    }

    /**
     * Get all active settlement networks as an array
     *
     * @return array
     * @throws AxiomePaymentsException
     */
    public function allAsArray(): array
    {
        $networks = $this->all();

        return array_map(function (Network $network) {
            return $network->toArray();
        }, $networks);
    }
}
