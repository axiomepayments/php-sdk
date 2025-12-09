# AxiomePayments PHP SDK Examples

## Installation

```bash
composer require axiomepayments/php-sdk
```

## Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use AxiomePayments\AxiomePayments;

$axiomepayments = new AxiomePayments([
    'api_key' => 'your-api-key',
    'api_secret' => 'your-api-secret',
    'environment' => 'sandbox', // or 'production'
]);
```

## Working with Currencies

Get all active processing currencies:

```php
// Get as objects
$currencies = $axiomepayments->currencies->all();

foreach ($currencies as $currency) {
    echo $currency->getName() . ' (' . $currency->getShortCode() . ')' . PHP_EOL;
    echo 'Symbol: ' . $currency->getSymbol() . PHP_EOL;
    echo 'USD Value: ' . $currency->getCurrentUsdValue() . PHP_EOL;
}

// Get as arrays
$currenciesArray = $axiomepayments->currencies->allAsArray();
```

## Working with Networks

Get all active settlement networks (blockchain networks):

```php
// Get as objects
$networks = $axiomepayments->networks->all();

foreach ($networks as $network) {
    echo $network->getName() . ' (' . $network->getShortCode() . ')' . PHP_EOL;
    echo 'Chain ID: ' . $network->getChainId() . PHP_EOL;
    echo 'Token Standard: ' . $network->getTokenStandard() . PHP_EOL;
    echo 'Explorer: ' . $network->getExplorerUrl() . PHP_EOL;
    echo 'Icon: ' . $network->getIconUrl() . PHP_EOL;
    echo 'Is Testnet: ' . ($network->isTestnet() ? 'Yes' : 'No') . PHP_EOL;
}

// Get as arrays
$networksArray = $axiomepayments->networks->allAsArray();
```

## Working with Commodities

Get all active settlement commodities (cryptocurrencies):

```php
// Get as objects
$commodities = $axiomepayments->commodities->all();

foreach ($commodities as $commodity) {
    echo $commodity->getName() . ' (' . $commodity->getShortCode() . ')' . PHP_EOL;
    echo 'Current USD Value: ' . $commodity->getCurrentUsdValue() . PHP_EOL;
    echo 'Network: ' . $commodity->getNetworkShortCode() . PHP_EOL;
    echo 'Contract Address: ' . $commodity->getContractAddress() . PHP_EOL;
    echo 'Decimals: ' . $commodity->getDecimals() . PHP_EOL;
    echo 'Icon: ' . $commodity->getIconUrl() . PHP_EOL;
    echo 'Is Native Token: ' . ($commodity->isNativeToken() ? 'Yes' : 'No') . PHP_EOL;
}

// Get as arrays
$commoditiesArray = $axiomepayments->commodities->allAsArray();
```

## Getting Conversion Rates

Use the conversion rate endpoint to get real-time exchange rates between fiat currencies and cryptocurrencies on specific networks. This endpoint requires **short codes** from networks, commodities, and currencies:

```php
// First, load all available options to get their short codes
$networks = $axiomepayments->networks->all();
$commodities = $axiomepayments->commodities->all();
$currencies = $axiomepayments->currencies->all();

// Display available options
echo "Available Networks:" . PHP_EOL;
foreach ($networks as $network) {
    echo "- {$network->getName()} (short_code: {$network->getShortCode()})" . PHP_EOL;
}

echo "\nAvailable Commodities:" . PHP_EOL;
foreach ($commodities as $commodity) {
    echo "- {$commodity->getName()} (short_code: {$commodity->getShortCode()}) on network: {$commodity->getNetworkShortCode()}" . PHP_EOL;
}

echo "\nAvailable Currencies:" . PHP_EOL;
foreach ($currencies as $currency) {
    echo "- {$currency->getName()} (short_code: {$currency->getShortCode()})" . PHP_EOL;
}

// Get conversion rate using short codes
// Parameters: network short_code, commodity short_code, currency short_code, payment method
$conversionRate = $axiomepayments->payments->getConversionRate('POL', 'USDT', 'USD', 'card');

echo "\nConversion Rate Data:" . PHP_EOL;
print_r($conversionRate);

// Example response:
// [
//     'rate' => '1'
// ]
```

### Complete Example: Building a Payment with Conversion Rates

```php
<?php

require_once 'vendor/autoload.php';

use AxiomePayments\AxiomePayments;

$axiomepayments = new AxiomePayments([
    'api_key' => 'your-api-key',
    'api_secret' => 'your-api-secret',
    'environment' => 'sandbox',
]);

// Step 1: Load available options
$networks = $axiomepayments->networks->all();
$commodities = $axiomepayments->commodities->all();
$currencies = $axiomepayments->currencies->all();

// Step 2: Find a specific commodity and its network
$usdtOnPolygon = null;
foreach ($commodities as $commodity) {
    if ($commodity->getShortCode() === 'USDT' && $commodity->getNetworkShortCode() === 'POL') {
        $usdtOnPolygon = $commodity;
        break;
    }
}

if (!$usdtOnPolygon) {
    die('USDT on Polygon not available');
}

// Step 3: Get the network details
$polygonNetwork = null;
foreach ($networks as $network) {
    if ($network->getShortCode() === $usdtOnPolygon->getNetworkShortCode()) {
        $polygonNetwork = $network;
        break;
    }
}

echo "Selected Configuration:" . PHP_EOL;
echo "Network: {$polygonNetwork->getName()} ({$polygonNetwork->getShortCode()})" . PHP_EOL;
echo "Commodity: {$usdtOnPolygon->getName()} ({$usdtOnPolygon->getShortCode()})" . PHP_EOL;
echo "Contract: {$usdtOnPolygon->getContractAddress()}" . PHP_EOL;
echo PHP_EOL;

// Step 4: Check conversion rates for different currencies
$fiatCurrencies = ['USD', 'EUR', 'GBP'];

foreach ($fiatCurrencies as $currencyCode) {
    try {
        $rate = $axiomepayments->payments->getConversionRate(
            $polygonNetwork->getShortCode(),  // Network short code: 'POL'
            $usdtOnPolygon->getShortCode(),   // Commodity short code: 'USDT'
            $currencyCode,                     // Currency short code: 'USD', 'EUR', etc.
            'card'                             // Payment method
        );

        echo "Conversion Rate for {$currencyCode}:" . PHP_EOL;
        echo "  Rate: {$rate['rate']}" . PHP_EOL;
        echo PHP_EOL;
    } catch (\Exception $e) {
        echo "Error getting rate for {$currencyCode}: {$e->getMessage()}" . PHP_EOL;
    }
}

// Step 5: Create a payment with the selected options
$payment = $axiomepayments->payments->create([
    'amount' => 100.00,
    'currency' => 'USD',
    'title' => 'Test Payment',
    'description' => 'Payment for order #12345',
    'success_url' => 'https://example.com/success',
    'cancel_url' => 'https://example.com/cancel',
]);

echo "Payment created!" . PHP_EOL;
echo "Payment URL: {$payment->getPaymentUrl()}" . PHP_EOL;
echo "Payment ID: {$payment->getId()}" . PHP_EOL;
```

### Finding Compatible Commodities for a Network

```php
// Get all commodities that operate on Polygon network
$polygonCommodities = array_filter(
    $axiomepayments->commodities->all(),
    fn($commodity) => $commodity->getNetworkShortCode() === 'POL'
);

echo "Commodities available on Polygon:" . PHP_EOL;
foreach ($polygonCommodities as $commodity) {
    echo "- {$commodity->getName()} ({$commodity->getShortCode()})" . PHP_EOL;
    echo "  Contract: " . ($commodity->getContractAddress() ?: 'Native Token') . PHP_EOL;
    echo "  Current USD Value: {$commodity->getCurrentUsdValue()}" . PHP_EOL;
}
```

## Working with Payments

Create a payment:

```php
$payment = $axiomepayments->payments->create([
    'amount' => 100.00,
    'currency' => 'USD',
    'title' => 'Test Payment',
    'description' => 'Payment for order #12345',
    'success_url' => 'https://example.com/success',
    'cancel_url' => 'https://example.com/cancel',
    'metadata' => [
        'order_id' => '12345',
        'customer_id' => 'cust_123',
    ],
]);

echo 'Payment URL: ' . $payment->getPaymentUrl() . PHP_EOL;
echo 'Payment ID: ' . $payment->getId() . PHP_EOL;
```

Get payment status:

```php
$status = $axiomepayments->payments->getStatus('payment-reference-id');

echo 'Status: ' . $status->getStatus() . PHP_EOL;
echo 'Amount: ' . $status->getFiatTotalAmount() . ' ' . $status->getFiatCurrency() . PHP_EOL;

if ($status->isPaid()) {
    echo 'Payment completed!' . PHP_EOL;
    echo 'Blockchain TX: ' . $status->getBlockchainTxHash() . PHP_EOL;
}
```

List payments:

```php
$paymentList = $axiomepayments->payments->list([
    'to_date' => '2024-12-31',
]);

foreach ($paymentList->getPayments() as $payment) {
    echo 'Payment: ' . $payment->getId() . ' - ' . $payment->getStatus() . PHP_EOL;
}

if ($paymentList->hasMore()) {
    echo 'More payments available. Next page token: ' . $paymentList->getNextPageToken() . PHP_EOL;
}
```

## Webhook Handling

```php
use AxiomePayments\Webhook;

// Get the webhook payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';

// Verify and parse the webhook
try {
    $webhook = Webhook::construct($payload, $signature, 'your-webhook-secret');

    $event = $webhook->getEvent();
    $data = $webhook->getData();

    switch ($event) {
        case 'payment_intent.succeeded':
            // Handle successful payment
            $transactionId = $data['transaction']['id'];
            echo "Payment succeeded: {$transactionId}" . PHP_EOL;
            break;

        case 'payment_intent.failed':
            // Handle failed payment
            $failReason = $data['fail_reason'];
            echo "Payment failed: {$failReason}" . PHP_EOL;
            break;
    }

} catch (\Exception $e) {
    echo 'Webhook verification failed: ' . $e->getMessage() . PHP_EOL;
    http_response_code(400);
}
```

## Error Handling

```php
use AxiomePayments\Exception\AxiomePaymentsException;
use AxiomePayments\Exception\AuthenticationException;
use AxiomePayments\Exception\InvalidRequestException;

try {
    $networks = $axiomepayments->networks->all();
} catch (AuthenticationException $e) {
    echo 'Authentication failed: ' . $e->getMessage() . PHP_EOL;
} catch (InvalidRequestException $e) {
    echo 'Invalid request: ' . $e->getMessage() . PHP_EOL;
} catch (AxiomePaymentsException $e) {
    echo 'API error: ' . $e->getMessage() . PHP_EOL;
}
```

## Laravel Integration

If you're using Laravel, the SDK will automatically register itself:

```php
use AxiomePayments\Laravel\Facades\AxiomePayments;

// Get networks
$networks = AxiomePayments::networks()->all();

// Get commodities
$commodities = AxiomePayments::commodities()->all();

// Create payment
$payment = AxiomePayments::payments()->create([
    'amount' => 100.00,
    'currency' => 'USD',
    'title' => 'Test Payment',
]);
```

Configuration in `config/axiomepayments.php`:

```php
return [
    'api_key' => env('AXIOMEPAYMENTS_API_KEY'),
    'api_secret' => env('AXIOMEPAYMENTS_API_SECRET'),
    'environment' => env('AXIOMEPAYMENTS_ENVIRONMENT', 'production'),
];
```
