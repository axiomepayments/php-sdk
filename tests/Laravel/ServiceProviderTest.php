<?php

namespace AxiomePayments\Tests\Laravel;

use Orchestra\Testbench\TestCase;
use AxiomePayments\Laravel\Facades\AxiomePayments;
use AxiomePayments\Laravel\AxiomePaymentsServiceProvider;
use AxiomePayments\Service\PaymentService;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [AxiomePaymentsServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'AxiomePayments' => AxiomePayments::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup default testing environment variables
        $app['config']->set('axiomepayments.api_key', 'test-key');
        $app['config']->set('axiomepayments.api_secret', 'test-secret');
        $app['config']->set('axiomepayments.environment', 'sandbox');
    }

    public function testServiceProviderRegistersConfig()
    {
        $this->assertNotNull(config('axiomepayments'));
        $this->assertEquals('test-key', config('axiomepayments.api_key'));
        $this->assertEquals('test-secret', config('axiomepayments.api_secret'));
        $this->assertEquals('sandbox', config('axiomepayments.environment'));
    }

    public function testServiceIsRegisteredAsSingleton()
    {
        $instance1 = app('axiomepayments');
        $instance2 = app('axiomepayments');
        
        $this->assertSame($instance1, $instance2);
    }

    public function testFacadeWorks()
    {
        // Need to get the root instance first
        $instance = AxiomePayments::getFacadeRoot();
        $this->assertInstanceOf(PaymentService::class, $instance->payments);
    }

    public function testCustomApiUrlFromEnvironment()
    {
        $customUrl = 'https://custom-api.axiomepayments.test/v1.0';
        
        $this->app['config']->set('axiomepayments.api_url', $customUrl);
        
        $baseUrl = AxiomePayments::getConfig('base_url');
        $this->assertEquals($customUrl, $baseUrl);
    }

    public function testDefaultApiUrlFallback()
    {
        // Clear any custom URL
        $this->app['config']->set('axiomepayments.api_url', null);
        
        $baseUrl = AxiomePayments::getConfig('base_url');
        $this->assertEquals('https://sandbox-api.axiomepayments.com/v1.0', $baseUrl);
    }

    public function testPaymentServiceIntegration()
    {
        $instance = AxiomePayments::getFacadeRoot();
        $payment = $instance->payments;
        
        $this->assertInstanceOf(PaymentService::class, $payment);
    }

    public function testEnvironmentOverride()
    {
        // Test production environment
        $this->app['config']->set('axiomepayments.environment', 'production');
        $this->app['config']->set('axiomepayments.api_url', null);
        
        $baseUrl = AxiomePayments::getConfig('base_url');
        $this->assertEquals('https://api.axiomepayments.com/v1.0', $baseUrl);
    }

    public function testConfigPublishing()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'AxiomePayments\Laravel\AxiomePaymentsServiceProvider',
            '--tag' => 'axiomepayments-config'
        ]);

        $this->assertFileExists(config_path('axiomepayments.php'));
    }
}