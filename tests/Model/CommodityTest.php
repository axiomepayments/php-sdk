<?php

namespace AxiomePayments\Tests\Model;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Model\Commodity;

class CommodityTest extends TestCase
{
    public function testFromArrayCreatesInstanceWithAllFields()
    {
        $data = [
            'short_code' => 'USDT',
            'name' => 'Tether USD',
            'icon_url' => 'https://axiomepayments.com/storage/commodities/USDT.webp',
            'current_usd_value' => '1.00000000',
            'contract_address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
            'decimals' => 6,
            'network_short_code' => 'POL',
        ];

        $commodity = Commodity::fromArray($data);

        $this->assertInstanceOf(Commodity::class, $commodity);
        $this->assertEquals('USDT', $commodity->short_code);
        $this->assertEquals('Tether USD', $commodity->name);
        $this->assertEquals('https://axiomepayments.com/storage/commodities/USDT.webp', $commodity->icon_url);
        $this->assertEquals('1.00000000', $commodity->current_usd_value);
        $this->assertEquals('0xc2132D05D31c914a87C6611C10748AEb04B58e8F', $commodity->contract_address);
        $this->assertEquals(6, $commodity->decimals);
        $this->assertEquals('POL', $commodity->network_short_code);
    }

    public function testFromArrayWithNativeToken()
    {
        $data = [
            'short_code' => 'MATIC',
            'name' => 'Polygon',
            'icon_url' => 'https://axiomepayments.com/storage/commodities/MATIC.webp',
            'current_usd_value' => '0.45000000',
            'contract_address' => null,
            'decimals' => 18,
            'network_short_code' => 'POL',
        ];

        $commodity = Commodity::fromArray($data);

        $this->assertEquals('MATIC', $commodity->short_code);
        $this->assertEquals('Polygon', $commodity->name);
        $this->assertEquals('0.45000000', $commodity->current_usd_value);
        $this->assertNull($commodity->contract_address);
        $this->assertEquals(18, $commodity->decimals);
        $this->assertEquals('POL', $commodity->network_short_code);
    }

    public function testFromArrayHandlesMissingFields()
    {
        $data = [
            'short_code' => 'ETH',
            'name' => 'Ethereum',
        ];

        $commodity = Commodity::fromArray($data);

        $this->assertEquals('ETH', $commodity->short_code);
        $this->assertEquals('Ethereum', $commodity->name);
        $this->assertNull($commodity->icon_url);
        $this->assertNull($commodity->current_usd_value);
        $this->assertNull($commodity->contract_address);
        $this->assertNull($commodity->decimals);
        $this->assertNull($commodity->network_short_code);
    }

    public function testToArrayReturnsAllFields()
    {
        $data = [
            'short_code' => 'USDT',
            'name' => 'Tether USD',
            'icon_url' => 'https://axiomepayments.com/storage/commodities/USDT.webp',
            'current_usd_value' => '1.00000000',
            'contract_address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
            'decimals' => 6,
            'network_short_code' => 'POL',
        ];

        $commodity = Commodity::fromArray($data);
        $array = $commodity->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('USDT', $array['short_code']);
        $this->assertEquals('Tether USD', $array['name']);
        $this->assertEquals('https://axiomepayments.com/storage/commodities/USDT.webp', $array['icon_url']);
        $this->assertEquals('1.00000000', $array['current_usd_value']);
        $this->assertEquals('0xc2132D05D31c914a87C6611C10748AEb04B58e8F', $array['contract_address']);
        $this->assertEquals(6, $array['decimals']);
        $this->assertEquals('POL', $array['network_short_code']);
    }

    public function testToArrayFiltersNullValues()
    {
        $data = [
            'short_code' => 'MATIC',
            'name' => 'Polygon',
            'contract_address' => null,
        ];

        $commodity = Commodity::fromArray($data);
        $array = $commodity->toArray();

        $this->assertArrayHasKey('short_code', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayNotHasKey('contract_address', $array); // null values are filtered out
    }

    public function testGettersReturnCorrectValues()
    {
        $data = [
            'short_code' => 'USDT',
            'name' => 'Tether USD',
            'icon_url' => 'https://axiomepayments.com/storage/commodities/USDT.webp',
            'current_usd_value' => '1.00000000',
            'contract_address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
            'decimals' => 6,
            'network_short_code' => 'POL',
        ];

        $commodity = Commodity::fromArray($data);

        $this->assertEquals('USDT', $commodity->getShortCode());
        $this->assertEquals('Tether USD', $commodity->getName());
        $this->assertEquals('https://axiomepayments.com/storage/commodities/USDT.webp', $commodity->getIconUrl());
        $this->assertEquals('1.00000000', $commodity->getCurrentUsdValue());
        $this->assertEquals('0xc2132D05D31c914a87C6611C10748AEb04B58e8F', $commodity->getContractAddress());
        $this->assertEquals(6, $commodity->getDecimals());
        $this->assertEquals('POL', $commodity->getNetworkShortCode());
    }

    public function testIsNativeTokenMethod()
    {
        $tokenCommodity = Commodity::fromArray([
            'short_code' => 'USDT',
            'name' => 'Tether USD',
            'contract_address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
        ]);

        $nativeCommodity = Commodity::fromArray([
            'short_code' => 'MATIC',
            'name' => 'Polygon',
            'contract_address' => null,
        ]);

        $emptyContractCommodity = Commodity::fromArray([
            'short_code' => 'ETH',
            'name' => 'Ethereum',
            'contract_address' => '',
        ]);

        $this->assertFalse($tokenCommodity->isNativeToken());
        $this->assertTrue($nativeCommodity->isNativeToken());
        $this->assertTrue($emptyContractCommodity->isNativeToken());
    }
}
