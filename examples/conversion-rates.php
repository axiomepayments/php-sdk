<?php

/**
 * Example: Loading Networks, Commodities, Currencies and Using Conversion Rates
 *
 * This example demonstrates how to:
 * 1. Load all available networks, commodities, and currencies
 * 2. Use their short_codes with the conversion rate endpoint
 * 3. Create a payment with the selected options
 */

require_once __DIR__ . '/../vendor/autoload.php';

use AxiomePayments\AxiomePayments;

// Initialize the SDK
$axiomepayments = new AxiomePayments([
    'api_key' => getenv('AXIOMEPAYMENTS_API_KEY') ?: 'your-api-key',
    'api_secret' => getenv('AXIOMEPAYMENTS_API_SECRET') ?: 'your-api-secret',
    'environment' => 'sandbox', // or 'production'
]);

echo "=== AxiomePayments Conversion Rate Example ===" . PHP_EOL . PHP_EOL;

// Step 1: Load all available options
echo "Step 1: Loading available networks, commodities, and currencies..." . PHP_EOL;

$networks = $axiomepayments->networks->all();
$commodities = $axiomepayments->commodities->all();
$currencies = $axiomepayments->currencies->all();

echo "✓ Loaded " . count($networks) . " networks" . PHP_EOL;
echo "✓ Loaded " . count($commodities) . " commodities" . PHP_EOL;
echo "✓ Loaded " . count($currencies) . " currencies" . PHP_EOL;
echo PHP_EOL;

// Step 2: Display available networks
echo "Step 2: Available Networks (use short_code for conversion rate endpoint)" . PHP_EOL;
echo str_repeat("-", 80) . PHP_EOL;

foreach ($networks as $network) {
    echo sprintf(
        "  • %s (short_code: %s) - Chain ID: %s %s" . PHP_EOL,
        $network->getName(),
        $network->getShortCode(),
        $network->getChainId() ?: 'N/A',
        $network->isTestnet() ? '[TESTNET]' : ''
    );
}
echo PHP_EOL;

// Step 3: Display available commodities
echo "Step 3: Available Commodities (use short_code for conversion rate endpoint)" . PHP_EOL;
echo str_repeat("-", 80) . PHP_EOL;

foreach ($commodities as $commodity) {
    $contract = $commodity->getContractAddress()
        ? substr($commodity->getContractAddress(), 0, 10) . '...'
        : 'Native Token';

    echo sprintf(
        "  • %s (short_code: %s) on %s - USD Value: %s - Contract: %s" . PHP_EOL,
        $commodity->getName(),
        $commodity->getShortCode(),
        $commodity->getNetworkShortCode(),
        $commodity->getCurrentUsdValue(),
        $contract
    );
}
echo PHP_EOL;

// Step 4: Display available currencies
echo "Step 4: Available Currencies (use short_code for conversion rate endpoint)" . PHP_EOL;
echo str_repeat("-", 80) . PHP_EOL;

foreach ($currencies as $currency) {
    echo sprintf(
        "  • %s (short_code: %s) - Symbol: %s - USD Value: %s" . PHP_EOL,
        $currency->getName(),
        $currency->getShortCode(),
        $currency->getSymbol(),
        $currency->getCurrentUsdValue()
    );
}
echo PHP_EOL;

// Step 5: Find a specific commodity and network combination
echo "Step 5: Finding USDT on Polygon network..." . PHP_EOL;

$usdtOnPolygon = null;
foreach ($commodities as $commodity) {
    if ($commodity->getShortCode() === 'USDT' && $commodity->getNetworkShortCode() === 'POL') {
        $usdtOnPolygon = $commodity;
        break;
    }
}

if (!$usdtOnPolygon) {
    die("✗ USDT on Polygon not available" . PHP_EOL);
}

$polygonNetwork = null;
foreach ($networks as $network) {
    if ($network->getShortCode() === $usdtOnPolygon->getNetworkShortCode()) {
        $polygonNetwork = $network;
        break;
    }
}

echo "✓ Found configuration:" . PHP_EOL;
echo "  Network: {$polygonNetwork->getName()} (short_code: {$polygonNetwork->getShortCode()})" . PHP_EOL;
echo "  Commodity: {$usdtOnPolygon->getName()} (short_code: {$usdtOnPolygon->getShortCode()})" . PHP_EOL;
echo "  Contract: {$usdtOnPolygon->getContractAddress()}" . PHP_EOL;
echo "  Chain ID: {$polygonNetwork->getChainId()}" . PHP_EOL;
echo PHP_EOL;

// Step 6: Get conversion rates for multiple currencies
echo "Step 6: Getting conversion rates using short codes..." . PHP_EOL;
echo str_repeat("-", 80) . PHP_EOL;

$fiatCurrencies = ['USD', 'EUR', 'GBP'];

foreach ($fiatCurrencies as $currencyCode) {
    try {
        // The conversion rate endpoint requires SHORT CODES for all parameters:
        // - $network: network short_code (e.g., 'POL', 'ETH', 'BSC')
        // - $commodity: commodity short_code (e.g., 'USDT', 'USDC', 'MATIC')
        // - $fiatCurrency: currency short_code (e.g., 'USD', 'EUR', 'GBP')
        // - $paymentMethod: payment method (e.g., 'card', 'bank_transfer')

        $rate = $axiomepayments->payments->getConversionRate(
            $polygonNetwork->getShortCode(),  // Network short_code: 'POL'
            $usdtOnPolygon->getShortCode(),   // Commodity short_code: 'USDT'
            $currencyCode,                     // Currency short_code: 'USD', 'EUR', 'GBP'
            'card'                             // Payment method
        );

        echo "✓ Conversion Rate for {$currencyCode}:" . PHP_EOL;
        echo "  Network: {$polygonNetwork->getShortCode()}" . PHP_EOL;
        echo "  Commodity: {$usdtOnPolygon->getShortCode()}" . PHP_EOL;
        echo "  Fiat Currency: {$currencyCode}" . PHP_EOL;
        echo "  Rate: {$rate['rate']}" . PHP_EOL;
        echo PHP_EOL;
    } catch (\Exception $e) {
        echo "✗ Error getting rate for {$currencyCode}: {$e->getMessage()}" . PHP_EOL;
        echo PHP_EOL;
    }
}

// Step 7: Optional - Create a payment
echo "Step 7: Creating a test payment..." . PHP_EOL;

try {
    $payment = $axiomepayments->payments->create([
        'amount' => 100.00,
        'currency' => 'USD',  // Use currency short_code
        'title' => 'Example Payment',
        'description' => 'Testing conversion rates with USDT on Polygon',
        'success_url' => 'https://example.com/success',
        'cancel_url' => 'https://example.com/cancel',
        'metadata' => [
            'network' => $polygonNetwork->getShortCode(),
            'commodity' => $usdtOnPolygon->getShortCode(),
        ],
    ]);

    echo "✓ Payment created successfully!" . PHP_EOL;
    echo "  Payment ID: {$payment->getId()}" . PHP_EOL;
    echo "  Payment URL: {$payment->getPaymentUrl()}" . PHP_EOL;
    echo "  Status: {$payment->getStatus()}" . PHP_EOL;
    echo PHP_EOL;
} catch (\Exception $e) {
    echo "✗ Error creating payment: {$e->getMessage()}" . PHP_EOL;
    echo PHP_EOL;
}

// Step 8: Show commodities grouped by network
echo "Step 8: Commodities grouped by network" . PHP_EOL;
echo str_repeat("-", 80) . PHP_EOL;

$commoditiesByNetwork = [];
foreach ($commodities as $commodity) {
    $networkCode = $commodity->getNetworkShortCode();
    if (!isset($commoditiesByNetwork[$networkCode])) {
        $commoditiesByNetwork[$networkCode] = [];
    }
    $commoditiesByNetwork[$networkCode][] = $commodity;
}

foreach ($commoditiesByNetwork as $networkCode => $networkCommodities) {
    // Find network name
    $networkName = $networkCode;
    foreach ($networks as $network) {
        if ($network->getShortCode() === $networkCode) {
            $networkName = $network->getName();
            break;
        }
    }

    echo "Network: {$networkName} ({$networkCode})" . PHP_EOL;
    foreach ($networkCommodities as $commodity) {
        echo "  • {$commodity->getName()} ({$commodity->getShortCode()})" . PHP_EOL;
    }
    echo PHP_EOL;
}

echo "=== Example Complete ===" . PHP_EOL;
