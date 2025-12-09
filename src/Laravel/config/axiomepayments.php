<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AxiomePayments API Configuration
    |--------------------------------------------------------------------------
    */

    // API Key from your AxiomePayments dashboard
    'api_key' => env('AXIOMEPAYMENTS_API_KEY'),

    // API Secret from your AxiomePayments dashboard
    'api_secret' => env('AXIOMEPAYMENTS_API_SECRET'),

    // Environment: 'sandbox' or 'production'
    'environment' => env('AXIOMEPAYMENTS_ENVIRONMENT', 'sandbox'),

    // Optional custom API URL (overrides the default URL for the environment)
    'api_url' => env('AXIOMEPAYMENTS_API_URL'),
];