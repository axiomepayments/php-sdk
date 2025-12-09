<?php

require_once __DIR__ . '/../vendor/autoload.php';

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
        // or check create_payment_full_prefill.php example to see what you can do with this:
        'customer_details' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com'
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