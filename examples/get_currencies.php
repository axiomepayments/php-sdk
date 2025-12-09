<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AxiomePayments\AxiomePayments;
use AxiomePayments\Exception\AxiomePaymentsException;

try {
    // Initialize AxiomePayments client
    $axiomepayments = new AxiomePayments([
        'api_key' => 'your-api-key',
        'api_secret' => 'your-api-secret',
        'environment' => 'sandbox'
    ]);

    echo "AxiomePayments - Get Available Currencies Example\n";
    echo str_repeat('=', 80) . "\n\n";

    // Get all available currencies as Currency objects
    $currencies = $axiomepayments->currencies->all();

    echo "Available Currencies (" . count($currencies) . " total):\n";
    echo str_repeat('-', 80) . "\n";

    foreach ($currencies as $currency) {
        echo "Currency Code: " . $currency->getShortCode() . "\n";
        echo "Name: " . $currency->getName() . "\n";
        echo "Symbol: " . $currency->getSymbol() . "\n";
        echo "USD Value: " . $currency->getCurrentUsdValue() . "\n";

        if ($currency->isProcessedViaAnotherCurrency()) {
            echo "Processed via: " . $currency->getProcessedViaCurrencyCode() . "\n";
        } else {
            echo "Processed via: Direct processing\n";
        }

        echo str_repeat('-', 80) . "\n";
    }

    // Example: Get currencies as simple arrays
    echo "\nGetting currencies as arrays...\n";
    $currenciesArray = $axiomepayments->currencies->allAsArray();

    echo "\nCurrencies as arrays:\n";
    echo json_encode($currenciesArray, JSON_PRETTY_PRINT) . "\n";

    // Example: Build a currency dropdown for a form
    echo "\nBuilding currency dropdown options:\n";
    echo "<select name=\"currency\">\n";
    foreach ($currencies as $currency) {
        echo "    <option value=\"" . htmlspecialchars($currency->getShortCode()) . "\">";
        echo htmlspecialchars($currency->getName()) . " (" . htmlspecialchars($currency->getSymbol()) . ")";
        echo "</option>\n";
    }
    echo "</select>\n";

    // Example: Filter currencies by processing method
    echo "\nCurrencies with direct processing:\n";
    $directCurrencies = array_filter($currencies, function($currency) {
        return !$currency->isProcessedViaAnotherCurrency();
    });

    foreach ($directCurrencies as $currency) {
        echo "- " . $currency->getShortCode() . ": " . $currency->getName() . "\n";
    }

    echo "\nCurrencies processed via another currency:\n";
    $indirectCurrencies = array_filter($currencies, function($currency) {
        return $currency->isProcessedViaAnotherCurrency();
    });

    foreach ($indirectCurrencies as $currency) {
        echo "- " . $currency->getShortCode() . ": " . $currency->getName();
        echo " (via " . $currency->getProcessedViaCurrencyCode() . ")\n";
    }

    // Example: Get specific currency information
    echo "\nLooking for USD currency details:\n";
    $usdCurrency = null;
    foreach ($currencies as $currency) {
        if ($currency->getShortCode() === 'USD') {
            $usdCurrency = $currency;
            break;
        }
    }

    if ($usdCurrency) {
        echo "Found USD:\n";
        echo "  Name: " . $usdCurrency->getName() . "\n";
        echo "  Symbol: " . $usdCurrency->getSymbol() . "\n";
        echo "  USD Value: " . $usdCurrency->getCurrentUsdValue() . "\n";
    } else {
        echo "USD currency not found.\n";
    }

} catch (AxiomePaymentsException $e) {
    echo "Error: " . $e->getMessage() . "\n";

    if ($e->getCode()) {
        echo "Error Code: " . $e->getCode() . "\n";
    }

    exit(1);
}
