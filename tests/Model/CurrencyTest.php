<?php

namespace AxiomePayments\Tests\Model;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Model\Currency;

class CurrencyTest extends TestCase
{
    public function testFromArrayCreatesInstanceWithAllFields()
    {
        $data = [
            'short_code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'current_usd_value' => '1.00000000',
            'processed_via_currency_code' => null,
        ];

        $currency = Currency::fromArray($data);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('USD', $currency->short_code);
        $this->assertEquals('US Dollar', $currency->name);
        $this->assertEquals('$', $currency->symbol);
        $this->assertEquals('1.00000000', $currency->current_usd_value);
        $this->assertNull($currency->processed_via_currency_code);
    }

    public function testFromArrayWithProcessedViaCurrency()
    {
        $data = [
            'short_code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'current_usd_value' => '0.92000000',
            'processed_via_currency_code' => 'USD',
        ];

        $currency = Currency::fromArray($data);

        $this->assertEquals('EUR', $currency->short_code);
        $this->assertEquals('Euro', $currency->name);
        $this->assertEquals('€', $currency->symbol);
        $this->assertEquals('0.92000000', $currency->current_usd_value);
        $this->assertEquals('USD', $currency->processed_via_currency_code);
    }

    public function testFromArrayHandlesMissingFields()
    {
        $data = [
            'short_code' => 'GBP',
            'name' => 'British Pound',
        ];

        $currency = Currency::fromArray($data);

        $this->assertEquals('GBP', $currency->short_code);
        $this->assertEquals('British Pound', $currency->name);
        $this->assertNull($currency->symbol);
        $this->assertNull($currency->current_usd_value);
        $this->assertNull($currency->processed_via_currency_code);
    }

    public function testToArrayReturnsAllFields()
    {
        $data = [
            'short_code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'current_usd_value' => '1.00000000',
            'processed_via_currency_code' => null,
        ];

        $currency = Currency::fromArray($data);
        $array = $currency->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('USD', $array['short_code']);
        $this->assertEquals('US Dollar', $array['name']);
        $this->assertEquals('$', $array['symbol']);
        $this->assertEquals('1.00000000', $array['current_usd_value']);
        $this->assertArrayNotHasKey('processed_via_currency_code', $array); // null values are filtered out
    }

    public function testGettersReturnCorrectValues()
    {
        $data = [
            'short_code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'current_usd_value' => '0.92000000',
            'processed_via_currency_code' => 'USD',
        ];

        $currency = Currency::fromArray($data);

        $this->assertEquals('EUR', $currency->getShortCode());
        $this->assertEquals('Euro', $currency->getName());
        $this->assertEquals('€', $currency->getSymbol());
        $this->assertEquals('0.92000000', $currency->getCurrentUsdValue());
        $this->assertEquals('USD', $currency->getProcessedViaCurrencyCode());
    }

    public function testIsProcessedViaAnotherCurrency()
    {
        $directCurrency = Currency::fromArray([
            'short_code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'current_usd_value' => '1.00000000',
            'processed_via_currency_code' => null,
        ]);

        $processedCurrency = Currency::fromArray([
            'short_code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'current_usd_value' => '0.92000000',
            'processed_via_currency_code' => 'USD',
        ]);

        $this->assertFalse($directCurrency->isProcessedViaAnotherCurrency());
        $this->assertTrue($processedCurrency->isProcessedViaAnotherCurrency());
    }
}
