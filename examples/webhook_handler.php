<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AxiomePayments\Webhook;
use AxiomePayments\Exception\AxiomePaymentsException;

try {
    // Initialize webhook handler with your webhook secret
    $webhook = new Webhook('your-webhook-secret');

    // Get the raw payload and signature
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_AXIOMEPAYMENTS_SIGNATURE'] ?? '';

    // Verify and parse the webhook
    $event = $webhook->verifyAndParse($payload, $signature);

    // Get event type and data
    $eventType = $webhook->getEventType($event);
    $eventData = $webhook->getEventData($event);

    // Log the webhook event
    error_log("Received webhook: {$eventType}");

    // Handle different event types
    switch ($eventType) {
        case 'payment_intent.created':
            handlePaymentIntentCreated($eventData);
            break;

        case 'payment_intent.attempting':
            handlePaymentIntentAttempting($eventData);
            break;

        case 'payment_intent.processing':
            handlePaymentIntentProcessing($eventData);
            break;
            
        case 'payment_intent.succeeded':
            handlePaymentIntentSucceeded($eventData);
            break;
            
        case 'payment_intent.failed':
            handlePaymentIntentFailed($eventData);
            break;
            
        case 'payment_intent.cancelled':
            handlePaymentIntentCancelled($eventData);
            break;
            
        case 'payment_intent.expired':
            handlePaymentIntentExpired($eventData);
            break;
            
        default:
            error_log("Unknown webhook event type: {$eventType}");
            break;
    }

    // Alternative way using helper methods
    if ($webhook->isPaymentIntentSucceeded($event)) {
        // Handle payment intent completion
        echo "Payment intent succeeded: " . $eventData['transaction']['reference_id'] . "\n";
    } elseif ($webhook->isPaymentIntentFailed($event)) {
        // Handle payment intent failure
        echo "Payment intent failed: " . $eventData['transaction']['reference_id'] . "\n";
    }

    // Return 200 OK to acknowledge receipt
    http_response_code(200);
    echo "OK";

} catch (AxiomePaymentsException $e) {
    error_log("Webhook error: " . $e->getMessage());
    http_response_code(400);
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    http_response_code(500);
    echo "Internal server error";
}

/**
 * Handle payment intent created event
 */
function handlePaymentIntentCreated(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    $amount = $transaction['amount'];
    $currency = $transaction['fiat_currency'];
    
    echo "Processing payment intent creation: {$referenceId} for {$amount} {$currency}\n";
    
    // Log payment intent
    // Initialize order status
    // etc.
    
    // Example database update (pseudo-code)
    // logPaymentIntent($transaction['id'], $referenceId);
    // initializeOrder($referenceId, $amount, $currency);
}

/**
 * Handle payment attempting event
 */
function handlePaymentIntentAttempting(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    $amount = $transaction['amount'];
    $currency = $transaction['fiat_currency'];
    
    echo "Processing payment attempting: {$referenceId} for {$amount} {$currency}\n";
    
    // Update your database
    
    // Example database update (pseudo-code)
    // updateOrderStatus($referenceId, 'attempting');
}

/**
 * Handle payment processing event
 */
function handlePaymentIntentProcessing(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    $amount = $transaction['amount'];
    $currency = $transaction['fiat_currency'];
    
    echo "Processing payment processing: {$referenceId} for {$amount} {$currency}\n";
    
    // Update your database
    
    // Example database update (pseudo-code)
    // updateOrderStatus($referenceId, 'processing');
}

/**
 * Handle payment intent succeeded event
 */
function handlePaymentIntentSucceeded(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    
    echo "Processing successful payment intent: {$referenceId}\n";
    
    // Update your database
    // Send confirmation email to customer
    // Fulfill the order
    // etc.
    
    // Example database update (pseudo-code)
    // updateOrderStatus($referenceId, 'completed');
    // if (isset($eventData['customer_details'])) {
    //     sendConfirmationEmail($eventData['customer_details']);
    // }
}

/**
 * Handle payment intent failed event
 */
function handlePaymentIntentFailed(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    
    echo "Processing failed payment intent: {$referenceId}\n";
    
    // Update your database
    // Send failure notification
    // etc.
    
    // Example database update (pseudo-code)
    // updateOrderStatus($referenceId, 'failed');
    // if (isset($eventData['customer_details'])) {
    //     sendFailureNotification($eventData['customer_details']);
    // }
}

/**
 * Handle payment intent cancelled event
 */
function handlePaymentIntentCancelled(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    
    echo "Processing cancelled payment intent: {$referenceId}\n";
    
    // Update your database
    // Handle cancellation
    // etc.
    
    // Example database update (pseudo-code)
    // handleOrderCancellation($referenceId);
    // releaseInventory($referenceId);
}

/**
 * Handle payment intent expired event
 */
function handlePaymentIntentExpired(array $eventData): void
{
    $transaction = $eventData['transaction'];
    $referenceId = $transaction['reference_id'];
    
    echo "Processing expired payment intent: {$referenceId}\n";
    
    // Clean up expired orders
    // Release inventory
    // etc.
    
    // Example cleanup (pseudo-code)
    // cleanupExpiredOrder($referenceId);
    // releaseInventory($referenceId);
}