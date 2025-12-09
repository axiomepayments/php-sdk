<?php

namespace AxiomePayments\Tests\Service;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Http\Client;
use AxiomePayments\Service\NetworkService;
use AxiomePayments\Model\Network;
use AxiomePayments\Exception\AxiomePaymentsException;

class NetworkServiceTest extends TestCase
{
    private $mockClient;
    private $networkService;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(Client::class);
        $this->networkService = new NetworkService($this->mockClient);
    }

    public function testAllReturnsArrayOfNetworks()
    {
        $responseData = [
            'data' => [
                [
                    'short_code' => 'POL',
                    'identifier' => 'polygon',
                    'name' => 'Polygon',
                    'token_standard' => 'ERC-20',
                    'chain_id' => '137',
                    'explorer_url' => 'https://polygonscan.com',
                    'icon_url' => 'https://axiomepayments.com/storage/networks/POL.webp',
                    'is_testnet' => false,
                ],
                [
                    'short_code' => 'ETH',
                    'identifier' => 'ethereum',
                    'name' => 'Ethereum',
                    'token_standard' => 'ERC-20',
                    'chain_id' => '1',
                    'explorer_url' => 'https://etherscan.io',
                    'icon_url' => 'https://axiomepayments.com/storage/networks/ETH.webp',
                    'is_testnet' => false,
                ],
                [
                    'short_code' => 'MATIC_AMOY',
                    'identifier' => 'polygon_amoy',
                    'name' => 'Polygon Amoy Testnet',
                    'token_standard' => 'ERC-20',
                    'chain_id' => '80002',
                    'explorer_url' => 'https://amoy.polygonscan.com',
                    'icon_url' => null,
                    'is_testnet' => true,
                ],
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/networks')
            ->willReturn($responseData);

        $networks = $this->networkService->all();

        $this->assertIsArray($networks);
        $this->assertCount(3, $networks);
        $this->assertContainsOnlyInstancesOf(Network::class, $networks);

        // Test first network (POL)
        $this->assertEquals('POL', $networks[0]->short_code);
        $this->assertEquals('polygon', $networks[0]->identifier);
        $this->assertEquals('Polygon', $networks[0]->name);
        $this->assertEquals('ERC-20', $networks[0]->token_standard);
        $this->assertEquals('137', $networks[0]->chain_id);
        $this->assertEquals('https://polygonscan.com', $networks[0]->explorer_url);
        $this->assertEquals('https://axiomepayments.com/storage/networks/POL.webp', $networks[0]->icon_url);
        $this->assertFalse($networks[0]->is_testnet);

        // Test second network (ETH)
        $this->assertEquals('ETH', $networks[1]->short_code);
        $this->assertEquals('ethereum', $networks[1]->identifier);
        $this->assertEquals('Ethereum', $networks[1]->name);
        $this->assertEquals('ERC-20', $networks[1]->token_standard);
        $this->assertFalse($networks[1]->is_testnet);

        // Test third network (testnet)
        $this->assertEquals('MATIC_AMOY', $networks[2]->short_code);
        $this->assertEquals('Polygon Amoy Testnet', $networks[2]->name);
        $this->assertTrue($networks[2]->is_testnet);
        $this->assertNull($networks[2]->icon_url);
    }

    public function testAllReturnsEmptyArrayWhenNoNetworks()
    {
        $responseData = [
            'data' => []
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/networks')
            ->willReturn($responseData);

        $networks = $this->networkService->all();

        $this->assertIsArray($networks);
        $this->assertEmpty($networks);
    }

    public function testAllAsArrayReturnsArrayOfArrays()
    {
        $responseData = [
            'data' => [
                [
                    'short_code' => 'POL',
                    'identifier' => 'polygon',
                    'name' => 'Polygon',
                    'token_standard' => 'ERC-20',
                    'chain_id' => '137',
                    'explorer_url' => 'https://polygonscan.com',
                    'icon_url' => 'https://axiomepayments.com/storage/networks/POL.webp',
                    'is_testnet' => false,
                ],
                [
                    'short_code' => 'ETH',
                    'identifier' => 'ethereum',
                    'name' => 'Ethereum',
                    'token_standard' => 'ERC-20',
                    'chain_id' => '1',
                    'explorer_url' => 'https://etherscan.io',
                    'icon_url' => 'https://axiomepayments.com/storage/networks/ETH.webp',
                    'is_testnet' => false,
                ],
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/networks')
            ->willReturn($responseData);

        $networks = $this->networkService->allAsArray();

        $this->assertIsArray($networks);
        $this->assertCount(2, $networks);

        // Check that all items are arrays
        foreach ($networks as $network) {
            $this->assertIsArray($network);
        }

        // Test first network
        $this->assertEquals('POL', $networks[0]['short_code']);
        $this->assertEquals('polygon', $networks[0]['identifier']);
        $this->assertEquals('Polygon', $networks[0]['name']);
        $this->assertEquals('ERC-20', $networks[0]['token_standard']);
        $this->assertEquals('137', $networks[0]['chain_id']);

        // Test second network
        $this->assertEquals('ETH', $networks[1]['short_code']);
        $this->assertEquals('ethereum', $networks[1]['identifier']);
        $this->assertEquals('Ethereum', $networks[1]['name']);
        $this->assertEquals('1', $networks[1]['chain_id']);
    }

    public function testAllThrowsExceptionOnInvalidResponse()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->willReturn(['error' => 'Some error']);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid response format from API');

        $this->networkService->all();
    }

    public function testAllAsArrayThrowsExceptionOnInvalidResponse()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->willReturn(['error' => 'Some error']);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid response format from API');

        $this->networkService->allAsArray();
    }
}
