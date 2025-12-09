# AxiomePayments PHP SDK Examples

This directory contains runnable examples demonstrating how to use the AxiomePayments PHP SDK.

## Setup

Before running any examples, make sure you have:

1. Installed the SDK dependencies:
```bash
cd /path/to/sdks/php
composer install
```

2. Set your API credentials as environment variables:
```bash
export AXIOMEPAYMENTS_API_KEY="your-api-key"
export AXIOMEPAYMENTS_API_SECRET="your-api-secret"
```

Or edit the example files directly to include your credentials.

## Examples

### conversion-rates.php

Demonstrates how to:
- Load all available networks, commodities, and currencies
- Use their `short_code` values with the conversion rate endpoint
- Get real-time conversion rates for different fiat currencies
- Create a payment with the selected configuration

**Run:**
```bash
php examples/conversion-rates.php
```

**Key Learning Points:**
- All endpoints (`/networks`, `/commodities`, `/currencies`) return objects with a `short_code` field
- The conversion rate endpoint requires **short codes** for all parameters:
  - Network parameter = network `short_code` (e.g., 'POL', 'ETH', 'BSC')
  - Commodity parameter = commodity `short_code` (e.g., 'USDT', 'USDC', 'MATIC')
  - Currency parameter = currency `short_code` (e.g., 'USD', 'EUR', 'GBP')

**Example API Call:**
```php
$rate = $axiomepayments->payments->getConversionRate(
    'POL',   // Network short_code
    'USDT',  // Commodity short_code
    'USD',   // Currency short_code
    'card'   // Payment method
);
```

## Environment

The examples default to the **sandbox** environment. To use production:

1. Change the environment in the example file:
```php
$axiomepayments = new AxiomePayments([
    'api_key' => 'your-api-key',
    'api_secret' => 'your-api-secret',
    'environment' => 'production', // Changed from 'sandbox'
]);
```

2. Make sure you're using production API credentials.

## Support

For more information, see:
- [Main Documentation](../EXAMPLES.md)
- [API Documentation](https://axiomepayments.com/api-documentation)
