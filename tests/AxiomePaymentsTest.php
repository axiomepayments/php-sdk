<?php

namespace AxiomePayments\Tests;

use PHPUnit\Framework\TestCase;
use AxiomePayments\AxiomePayments;
use AxiomePayments\Exception\AxiomePaymentsException;

class AxiomePaymentsTest extends TestCase
{
    public function testCanCreateAxiomePaymentsInstance()
    {
        $config = [
            'api_key' => 'test-key',
            'api_secret' => 'test-secret',
            'environment' => 'sandbox'
        ];

        $axiomepayments = new AxiomePayments($config);
        
        $this->assertInstanceOf(AxiomePayments::class, $axiomepayments);
        $this->assertEquals('sandbox', $axiomepayments->getConfig('environment'));
        $this->assertEquals('https://sandbox-api.axiomepayments.com/v1.0', $axiomepayments->getConfig('base_url'));
    }

    public function testProductionEnvironmentSetsCorrectBaseUrl()
    {
        $config = [
            'api_key' => 'test-key',
            'api_secret' => 'test-secret',
            'environment' => 'production'
        ];

        $axiomepayments = new AxiomePayments($config);
        
        $this->assertEquals('https://api.axiomepayments.com/v1.0', $axiomepayments->getConfig('base_url'));
    }

    public function testThrowsExceptionForMissingApiKey()
    {
        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Missing required config: api_key');

        new AxiomePayments([
            'api_secret' => 'test-secret'
        ]);
    }

    public function testThrowsExceptionForMissingApiSecret()
    {
        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Missing required config: api_secret');

        new AxiomePayments([
            'api_key' => 'test-key'
        ]);
    }

    public function testThrowsExceptionForInvalidEnvironment()
    {
        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Invalid environment. Must be \'sandbox\' or \'production\'');

        new AxiomePayments([
            'api_key' => 'test-key',
            'api_secret' => 'test-secret',
            'environment' => 'invalid'
        ]);
    }

    public function testCanAccessPaymentsService()
    {
        $config = [
            'api_key' => 'test-key',
            'api_secret' => 'test-secret'
        ];

        $axiomepayments = new AxiomePayments($config);

        $this->assertInstanceOf(\AxiomePayments\Service\PaymentService::class, $axiomepayments->payments);
    }

    public function testCanAccessCurrenciesService()
    {
        $config = [
            'api_key' => 'test-key',
            'api_secret' => 'test-secret'
        ];

        $axiomepayments = new AxiomePayments($config);

        $this->assertInstanceOf(\AxiomePayments\Service\CurrencyService::class, $axiomepayments->currencies);
    }

    public function testThrowsExceptionForUnknownService()
    {
        $config = [
            'api_key' => 'test-key',
            'api_secret' => 'test-secret'
        ];

        $axiomepayments = new AxiomePayments($config);

        $this->expectException(AxiomePaymentsException::class);
        $this->expectExceptionMessage('Unknown service: unknown');

        $axiomepayments->unknown;
    }
}