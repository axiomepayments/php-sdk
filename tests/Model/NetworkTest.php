<?php

namespace AxiomePayments\Tests\Model;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Model\Network;

class NetworkTest extends TestCase
{
    public function testFromArrayCreatesInstanceWithAllFields()
    {
        $data = [
            'short_code' => 'POL',
            'identifier' => 'polygon',
            'name' => 'Polygon',
            'token_standard' => 'ERC-20',
            'chain_id' => '137',
            'explorer_url' => 'https://polygonscan.com',
            'icon_url' => 'https://axiomepayments.com/storage/networks/POL.webp',
            'is_testnet' => false,
        ];

        $network = Network::fromArray($data);

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals('POL', $network->short_code);
        $this->assertEquals('polygon', $network->identifier);
        $this->assertEquals('Polygon', $network->name);
        $this->assertEquals('ERC-20', $network->token_standard);
        $this->assertEquals('137', $network->chain_id);
        $this->assertEquals('https://polygonscan.com', $network->explorer_url);
        $this->assertEquals('https://axiomepayments.com/storage/networks/POL.webp', $network->icon_url);
        $this->assertFalse($network->is_testnet);
    }

    public function testFromArrayWithTestnet()
    {
        $data = [
            'short_code' => 'MATIC_AMOY',
            'identifier' => 'polygon_amoy',
            'name' => 'Polygon Amoy Testnet',
            'token_standard' => 'ERC-20',
            'chain_id' => '80002',
            'explorer_url' => 'https://amoy.polygonscan.com',
            'icon_url' => null,
            'is_testnet' => true,
        ];

        $network = Network::fromArray($data);

        $this->assertEquals('MATIC_AMOY', $network->short_code);
        $this->assertEquals('polygon_amoy', $network->identifier);
        $this->assertEquals('Polygon Amoy Testnet', $network->name);
        $this->assertTrue($network->is_testnet);
        $this->assertNull($network->icon_url);
    }

    public function testFromArrayHandlesMissingFields()
    {
        $data = [
            'short_code' => 'ETH',
            'name' => 'Ethereum',
        ];

        $network = Network::fromArray($data);

        $this->assertEquals('ETH', $network->short_code);
        $this->assertEquals('Ethereum', $network->name);
        $this->assertNull($network->identifier);
        $this->assertNull($network->token_standard);
        $this->assertNull($network->chain_id);
        $this->assertNull($network->explorer_url);
        $this->assertNull($network->icon_url);
        $this->assertFalse($network->is_testnet); // Defaults to false when not provided
    }

    public function testToArrayReturnsAllFields()
    {
        $data = [
            'short_code' => 'POL',
            'identifier' => 'polygon',
            'name' => 'Polygon',
            'token_standard' => 'ERC-20',
            'chain_id' => '137',
            'explorer_url' => 'https://polygonscan.com',
            'icon_url' => 'https://axiomepayments.com/storage/networks/POL.webp',
            'is_testnet' => false,
        ];

        $network = Network::fromArray($data);
        $array = $network->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('POL', $array['short_code']);
        $this->assertEquals('polygon', $array['identifier']);
        $this->assertEquals('Polygon', $array['name']);
        $this->assertEquals('ERC-20', $array['token_standard']);
        $this->assertEquals('137', $array['chain_id']);
        $this->assertEquals('https://polygonscan.com', $array['explorer_url']);
        $this->assertEquals('https://axiomepayments.com/storage/networks/POL.webp', $array['icon_url']);
        $this->assertFalse($array['is_testnet']);
    }

    public function testToArrayFiltersNullValues()
    {
        $data = [
            'short_code' => 'ETH',
            'name' => 'Ethereum',
            'icon_url' => null,
        ];

        $network = Network::fromArray($data);
        $array = $network->toArray();

        $this->assertArrayHasKey('short_code', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayNotHasKey('icon_url', $array);
        // is_testnet defaults to false, so it will be included
        $this->assertArrayHasKey('is_testnet', $array);
        $this->assertFalse($array['is_testnet']);
    }

    public function testGettersReturnCorrectValues()
    {
        $data = [
            'short_code' => 'POL',
            'identifier' => 'polygon',
            'name' => 'Polygon',
            'token_standard' => 'ERC-20',
            'chain_id' => '137',
            'explorer_url' => 'https://polygonscan.com',
            'icon_url' => 'https://axiomepayments.com/storage/networks/POL.webp',
            'is_testnet' => false,
        ];

        $network = Network::fromArray($data);

        $this->assertEquals('POL', $network->getShortCode());
        $this->assertEquals('polygon', $network->getIdentifier());
        $this->assertEquals('Polygon', $network->getName());
        $this->assertEquals('ERC-20', $network->getTokenStandard());
        $this->assertEquals('137', $network->getChainId());
        $this->assertEquals('https://polygonscan.com', $network->getExplorerUrl());
        $this->assertEquals('https://axiomepayments.com/storage/networks/POL.webp', $network->getIconUrl());
        $this->assertFalse($network->isTestnet());
    }

    public function testIsTestnetMethod()
    {
        $mainnetNetwork = Network::fromArray([
            'short_code' => 'POL',
            'name' => 'Polygon',
            'is_testnet' => false,
        ]);

        $testnetNetwork = Network::fromArray([
            'short_code' => 'MATIC_AMOY',
            'name' => 'Polygon Amoy Testnet',
            'is_testnet' => true,
        ]);

        $this->assertFalse($mainnetNetwork->isTestnet());
        $this->assertTrue($testnetNetwork->isTestnet());
    }

    public function testIsTestnetDefaultsToFalseWhenNotProvided()
    {
        $network = Network::fromArray([
            'short_code' => 'ETH',
            'name' => 'Ethereum',
        ]);

        $this->assertFalse($network->isTestnet());
    }
}
