<?php

namespace AxiomePayments\Tests\Service;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Http\Client;
use AxiomePayments\Service\CommodityService;
use AxiomePayments\Model\Commodity;
use AxiomePayments\Exception\AxiomePaymentsException;

class CommodityServiceTest extends TestCase
{
    private $mockClient;
    private $commodityService;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(Client::class);
        $this->commodityService = new CommodityService($this->mockClient);
    }

    public function testAllReturnsArrayOfCommodities()
    {
        $responseData = [
            'data' => [
                [
                    'short_code' => 'USDT',
                    'name' => 'Tether USD',
                    'icon_url' => 'https://axiomepayments.com/storage/commodities/USDT.webp',
                    'current_usd_value' => '1.00000000',
                    'contract_address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
                    'decimals' => 6,
                    'network_short_code' => 'POL',
                ],
                [
                    'short_code' => 'USDC',
                    'name' => 'USD Coin',
                    'icon_url' => 'https://axiomepayments.com/storage/commodities/USDC.webp',
                    'current_usd_value' => '1.00000000',
                    'contract_address' => '0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174',
                    'decimals' => 6,
                    'network_short_code' => 'POL',
                ],
                [
                    'short_code' => 'MATIC',
                    'name' => 'Polygon',
                    'icon_url' => 'https://axiomepayments.com/storage/commodities/MATIC.webp',
                    'current_usd_value' => '0.45000000',
                    'contract_address' => null,
                    'decimals' => 18,
                    'network_short_code' => 'POL',
                ],
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/commodities')
            ->willReturn($responseData);

        $commodities = $this->commodityService->all();

        $this->assertIsArray($commodities);
        $this->assertCount(3, $commodities);
        $this->assertContainsOnlyInstancesOf(Commodity::class, $commodities);

        // Test first commodity (USDT)
        $this->assertEquals('USDT', $commodities[0]->short_code);
        $this->assertEquals('Tether USD', $commodities[0]->name);
        $this->assertEquals('https://axiomepayments.com/storage/commodities/USDT.webp', $commodities[0]->icon_url);
        $this->assertEquals('1.00000000', $commodities[0]->current_usd_value);
        $this->assertEquals('0xc2132D05D31c914a87C6611C10748AEb04B58e8F', $commodities[0]->contract_address);
        $this->assertEquals(6, $commodities[0]->decimals);
        $this->assertEquals('POL', $commodities[0]->network_short_code);
        $this->assertFalse($commodities[0]->isNativeToken());

        // Test second commodity (USDC)
        $this->assertEquals('USDC', $commodities[1]->short_code);
        $this->assertEquals('USD Coin', $commodities[1]->name);
        $this->assertFalse($commodities[1]->isNativeToken());

        // Test third commodity (MATIC - native token)
        $this->assertEquals('MATIC', $commodities[2]->short_code);
        $this->assertEquals('Polygon', $commodities[2]->name);
        $this->assertNull($commodities[2]->contract_address);
        $this->assertTrue($commodities[2]->isNativeToken());
        $this->assertEquals(18, $commodities[2]->decimals);
    }

    public function testAllReturnsEmptyArrayWhenNoCommodities()
    {
        $responseData = [
            'data' => []
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/commodities')
            ->willReturn($responseData);

        $commodities = $this->commodityService->all();

        $this->assertIsArray($commodities);
        $this->assertEmpty($commodities);
    }

    public function testAllAsArrayReturnsArrayOfArrays()
    {
        $responseData = [
            'data' => [
                [
                    'short_code' => 'USDT',
                    'name' => 'Tether USD',
                    'icon_url' => 'https://axiomepayments.com/storage/commodities/USDT.webp',
                    'current_usd_value' => '1.00000000',
                    'contract_address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
                    'decimals' => 6,
                    'network_short_code' => 'POL',
                ],
                [
                    'short_code' => 'USDC',
                    'name' => 'USD Coin',
                    'icon_url' => 'https://axiomepayments.com/storage/commodities/USDC.webp',
                    'current_usd_value' => '1.00000000',
                    'contract_address' => '0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174',
                    'decimals' => 6,
                    'network_short_code' => 'POL',
                ],
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/commodities')
            ->willReturn($responseData);

        $commodities = $this->commodityService->allAsArray();

        $this->assertIsArray($commodities);
        $this->assertCount(2, $commodities);

        // Check that all items are arrays
        foreach ($commodities as $commodity) {
            $this->assertIsArray($commodity);
        }

        // Test first commodity
        $this->assertEquals('USDT', $commodities[0]['short_code']);
        $this->assertEquals('Tether USD', $commodities[0]['name']);
        $this->assertEquals('1.00000000', $commodities[0]['current_usd_value']);
        $this->assertEquals(6, $commodities[0]['decimals']);
        $this->assertEquals('POL', $commodities[0]['network_short_code']);

        // Test second commodity
        $this->assertEquals('USDC', $commodities[1]['short_code']);
        $this->assertEquals('USD Coin', $commodities[1]['name']);
        $this->assertEquals('0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174', $commodities[1]['contract_address']);
    }

    public function testAllThrowsExceptionOnInvalidResponse()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->willReturn(['error' => 'Some error']);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid response format from API');

        $this->commodityService->all();
    }

    public function testAllAsArrayThrowsExceptionOnInvalidResponse()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->willReturn(['error' => 'Some error']);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid response format from API');

        $this->commodityService->allAsArray();
    }
}
