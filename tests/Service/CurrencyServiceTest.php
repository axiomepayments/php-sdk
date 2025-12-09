<?php

namespace AxiomePayments\Tests\Service;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Http\Client;
use AxiomePayments\Service\CurrencyService;
use AxiomePayments\Model\Currency;
use AxiomePayments\Exception\AxiomePaymentsException;

class CurrencyServiceTest extends TestCase
{
    private $mockClient;
    private $currencyService;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(Client::class);
        $this->currencyService = new CurrencyService($this->mockClient);
    }

    public function testAllReturnsArrayOfCurrencies()
    {
        $responseData = [
            'data' => [
                [
                    'short_code' => 'USD',
                    'name' => 'US Dollar',
                    'symbol' => '$',
                    'current_usd_value' => '1.00000000',
                    'processed_via_currency_code' => null,
                ],
                [
                    'short_code' => 'EUR',
                    'name' => 'Euro',
                    'symbol' => '€',
                    'current_usd_value' => '0.92000000',
                    'processed_via_currency_code' => 'USD',
                ],
                [
                    'short_code' => 'GBP',
                    'name' => 'British Pound',
                    'symbol' => '£',
                    'current_usd_value' => '0.79000000',
                    'processed_via_currency_code' => null,
                ],
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/currencies')
            ->willReturn($responseData);

        $currencies = $this->currencyService->all();

        $this->assertIsArray($currencies);
        $this->assertCount(3, $currencies);
        $this->assertContainsOnlyInstancesOf(Currency::class, $currencies);

        // Test first currency (USD)
        $this->assertEquals('USD', $currencies[0]->short_code);
        $this->assertEquals('US Dollar', $currencies[0]->name);
        $this->assertEquals('$', $currencies[0]->symbol);
        $this->assertEquals('1.00000000', $currencies[0]->current_usd_value);
        $this->assertNull($currencies[0]->processed_via_currency_code);

        // Test second currency (EUR)
        $this->assertEquals('EUR', $currencies[1]->short_code);
        $this->assertEquals('Euro', $currencies[1]->name);
        $this->assertEquals('€', $currencies[1]->symbol);
        $this->assertEquals('0.92000000', $currencies[1]->current_usd_value);
        $this->assertEquals('USD', $currencies[1]->processed_via_currency_code);

        // Test third currency (GBP)
        $this->assertEquals('GBP', $currencies[2]->short_code);
        $this->assertEquals('British Pound', $currencies[2]->name);
        $this->assertEquals('£', $currencies[2]->symbol);
    }

    public function testAllReturnsEmptyArrayWhenNoCurrencies()
    {
        $responseData = [
            'data' => []
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/currencies')
            ->willReturn($responseData);

        $currencies = $this->currencyService->all();

        $this->assertIsArray($currencies);
        $this->assertEmpty($currencies);
    }

    public function testAllAsArrayReturnsArrayOfArrays()
    {
        $responseData = [
            'data' => [
                [
                    'short_code' => 'USD',
                    'name' => 'US Dollar',
                    'symbol' => '$',
                    'current_usd_value' => '1.00000000',
                    'processed_via_currency_code' => null,
                ],
                [
                    'short_code' => 'EUR',
                    'name' => 'Euro',
                    'symbol' => '€',
                    'current_usd_value' => '0.92000000',
                    'processed_via_currency_code' => 'USD',
                ],
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/currencies')
            ->willReturn($responseData);

        $currencies = $this->currencyService->allAsArray();

        $this->assertIsArray($currencies);
        $this->assertCount(2, $currencies);

        // Check that all items are arrays
        foreach ($currencies as $currency) {
            $this->assertIsArray($currency);
        }

        // Test first currency
        $this->assertEquals('USD', $currencies[0]['short_code']);
        $this->assertEquals('US Dollar', $currencies[0]['name']);
        $this->assertEquals('$', $currencies[0]['symbol']);

        // Test second currency
        $this->assertEquals('EUR', $currencies[1]['short_code']);
        $this->assertEquals('Euro', $currencies[1]['name']);
        $this->assertEquals('€', $currencies[1]['symbol']);
        $this->assertEquals('USD', $currencies[1]['processed_via_currency_code']);
    }

    public function testAllThrowsExceptionOnInvalidResponse()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->willReturn(['error' => 'Some error']);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid response format from API');

        $this->currencyService->all();
    }

    public function testAllAsArrayThrowsExceptionOnInvalidResponse()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->willReturn(['error' => 'Some error']);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid response format from API');

        $this->currencyService->allAsArray();
    }
}
