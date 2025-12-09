<?php

namespace AxiomePayments\Laravel;

use Illuminate\Support\ServiceProvider;
use AxiomePayments\AxiomePayments;

class AxiomePaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/axiomepayments.php', 'axiomepayments');

        $this->app->singleton(AxiomePayments::class, function ($app) {
            $config = $app['config']['axiomepayments'];

            return new AxiomePayments([
                'api_key' => $config['api_key'],
                'api_secret' => $config['api_secret'],
                'environment' => $config['environment'],
                'base_url' => $config['api_url'] ?? null,
            ]);
        });

        $this->app->alias(AxiomePayments::class, 'axiomepayments');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/axiomepayments.php' => config_path('axiomepayments.php'),
            ], 'axiomepayments-config');
        }
    }
}