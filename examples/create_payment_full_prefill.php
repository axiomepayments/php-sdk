<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Read more about this here: https://axiomepayments.com/api-documentation#pre_fill

use AxiomePayments\AxiomePayments;
use AxiomePayments\Exception\AxiomePaymentsException;

try {
    // Initialize AxiomePayments client
    $axiomepayments = new AxiomePayments([
        'api_key' => 'your-api-key',
        'api_secret' => 'your-api-secret',
        'environment' => 'sandbox' // Use 'production' for live transactions
    ]);

    // Create a payment
    $payment = $axiomepayments->payments->create([
        'title' => 'My NFT',
        'amount' => 99.99,
        'currency' => 'USD',

        //'redirect_url' => 'https://yourstore.com/status',
        // OR:
        'success_url' => 'https://yourstore.com/success',
        'cancel_url' => 'https://yourstore.com/cancel',

        // read https://axiomepayments.com/api-documentation#pre_fill
        'customer_details' => [
            'first_name' => 'John',
            'middle_name' => 'Jay', // optional, skip if none
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+11234567890',
            'date_of_birth' => '1990-01-01',
            'country_of_residence' => 'US', // if present, has to be a valid alpha-2 country short code

            'state_of_residence' => 'MT',
            // rules for usage of "state_of_residence":
            // - if present, has to be a valid US state short code (alpha-2 uppercase)
            // - required if "country_of_residence" is "US" (we will ask for it if you don't prefill it)
            // - has to be dropped (not be in the payload) when "country_of_residence" is not "US"

            // if you want to prefill card information
            'card_country_code' => 'US', // if present, has to be a valid alpha-2 country short code
            'card_city' => 'Montana',

            'card_state_code' => 'MT',
            // rules for usage of "card_state_code":
            // - if present, has to be a valid US state short code (alpha-2 uppercase)
            // - required if "card_country_code" is "US" (we will ask for it if you don't prefill it)
            // - has to be dropped (not be in the payload) when "card_country_code" is not "US"

            'card_post_code' => '12345',
            'card_street' => 'Street 123',
        ],
        // Use metadata to identify customer/session - will be returned in webhooks and API responses
        'metadata' => [
            // example data:
            'order_id' => 'order_123',
            'product' => 'Digital Product'
        ],
        'lang' => 'en' // the language for the payment page - possible values: en, es, fr, de, it, pt, ru, zh, ja, ko, tr, ka
    ]);

    echo "Payment created successfully!\n";
    echo "Payment URL: " . $payment->payment_url . "\n";
    echo "Reference ID: " . $payment->reference_id . "\n";
    echo "Transaction ID: " . $payment->transaction_id . "\n";
    echo "Amount: " . $payment->amount . " " . $payment->currency . "\n";
    echo "Status: " . $payment->status . "\n";
    
    // In a web application, you would redirect the user to the payment URL
    // header('Location: ' . $payment->payment_url);
    // exit;

} catch (AxiomePaymentsException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 