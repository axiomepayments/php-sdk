# AxiomePayments PHP SDK

**Enterprise Payment Infrastructure**

Professional-grade card processing with instant cryptocurrency settlements. Built for businesses that demand reliability.

Official PHP SDK for the AxiomePayments payment processing API.

## Installation

Install via Composer:

```bash
composer require axiomepayments/php-sdk
```

## Requirements

- PHP 8.1 or higher
- ext-json
- GuzzleHTTP 7.0+

## Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use AxiomePayments\AxiomePayments;

// Initialize the client
$axiomepayments = new AxiomePayments([
    'api_key' => 'your-api-key',
    'api_secret' => 'your-api-secret',
    'environment' => 'production' // or 'sandbox'
]);

// Create a payment
$payment = $axiomepayments->payments->create([
    'amount' => 99.99,
    'currency' => 'USD',
    'title' => 'Payment for Order #123', // Required - title of the payment link
    'customer_details' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'customer@example.com'
    ],

    //'redirect_url' => 'https://yourstore.com/status',
    // OR:
    'success_url' => 'https://yourstore.com/success',
    'cancel_url' => 'https://yourstore.com/cancel',

]);

// Redirect to payment page
header('Location: ' . $payment->payment_url);

// OR for iframe embedding (fullscreen with transparent background):
// echo '<iframe src="' . $payment->embed_url . '" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; border: none; z-index: 9999;" allowpaymentrequest></iframe>';
exit;
```

## Configuration

### Environment

The SDK supports two environments:

- `sandbox` - For testing (https://sandbox-api.axiomepayments.com)
- `production` - For live transactions (https://api.axiomepayments.com)

### API Credentials

Get your API credentials from your AxiomePayments merchant dashboard:

1. Log in to your merchant account
2. Navigate to Sales Channels
3. Generate API credentials for your sales channel

## Payment Methods

### Create Payment

```php
$payment = $axiomepayments->payments->create([
    'amount' => 100.00,
    'currency' => 'USD',
    'title' => 'Digital Product Purchase', // Required - title of the payment link

    //'redirect_url' => 'https://yourstore.com/status',
    // OR:
    'success_url' => 'https://yourstore.com/success',
    'cancel_url' => 'https://yourstore.com/cancel',

    'customer_details' => [
        // learn more about this at: https://axiomepayments.com/api-documentation#pre_fill or examples/create_payment_full_prefill.php
        'first_name' => 'John',
        'middle_name' => 'Jay',              // Optional
        'last_name' => 'Doe',

        'email' => 'john@example.com',       // Optional
        'phone' => '+1234567890',            // Optional
        'date_of_birth' => '1990-01-01',     // Optional - YYYY-MM-DD format
        'country_of_residence' => 'US',      // Optional - if present, has to be alpha-2 country short code
        'state_of_residence' => 'CA',        // Optional - Required for US customers
        // rules for usage of "state_of_residence":
        // - if present, has to be a valid US state short code (alpha-2 uppercase)
        // - required if "country_of_residence" is "US" (we will ask for it if you don't prefill it)
        // - has to be dropped (not be in the payload) when "country_of_residence" is not "US"

        'card_country_code' => 'US',         // Optional - alpha-2 country short code card billing address
        'card_city' => 'California',         // Optional - prefill card billing address
        
        'card_state_code' => 'CA',           // Optional - US state alpha-2 short code card billing address
        // rules for usage of "card_state_code":
        // - if present, has to be a valid US state short code (alpha-2 uppercase)
        // - required if "card_country_code" is "US" (we will ask for it if you don't prefill it)
        // - has to be dropped (not be in the payload) when "card_country_code" is not "US"

        'card_post_code' => '12345',         // Optional - zip/post code card billing address
        'card_street' => 'Street 123',       // Optional - address line 1 (& 2) card billing address
    ],
    'metadata' => [
        // Optional - use this to identify the customer or payment session
        // This data will be returned in webhooks and API responses
        'order_id' => 'order_123',
        'product_id' => 'prod_456',
        'user_id' => 'user_789',
        'session_id' => 'sess_abc'
    ],
    'lang' => 'en' // the language for the payment page - possible values: en, es, fr, de, it, pt, ru, zh, ja, ko, tr
]);

echo "Payment URL: " . $payment->payment_url;
echo "Reference ID: " . $payment->reference_id;
```

### Get Payment Status

```php
$payment = $axiomepayments->payments->status('txn_abc123def456');

echo "Status: " . $payment->status;
echo "Amount: " . $payment->amount;
echo "Currency: " . $payment->currency;
```

### List Payments

```php
$payments = $axiomepayments->payments->list([
    'limit' => 20,
    'status' => 'completed',
    'from_date' => '2024-01-01',
    'to_date' => '2024-01-31'
]);

foreach ($payments->payments as $payment) {
    echo "Payment {$payment->reference_id}: {$payment->status}\n";
}

// Handle pagination
if ($payments->has_more) {
    $nextPage = $axiomepayments->payments->list([
        'page_token' => $payments->next_page_token
    ]);
}
```

### Get Available Currencies

```php
// Get all supported currencies
$currencies = $axiomepayments->currencies->all();

foreach ($currencies as $currency) {
    echo "{$currency->short_code}: {$currency->name} ({$currency->symbol})\n";
}

// Use in a payment form to build a currency dropdown
$currenciesArray = $axiomepayments->currencies->allAsArray();
```

## Webhook Handling

### Verify Webhook Signature

```php
<?php
// webhook.php
require_once 'vendor/autoload.php';

use AxiomePayments\Webhook;

$webhook = new Webhook('your-webhook-secret');

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';

if ($webhook->verifySignature($payload, $signature)) {
    $event = json_decode($payload, true);
    
    switch ($event['event']) {
        case 'payment_intent.created':
            // Handle successful payment
            $transaction = $event['data']['transaction'];
            echo "Payment intent created: " . $transaction['reference_id'];
            break;

        case 'payment_intent.attempting':
            // Handle a payment being attempted
            $transaction = $event['data']['transaction'];
            echo "Payment attempting: " . $transaction['reference_id'];
            break;

        case 'payment_intent.processing':
            // Handle successful payment
            $transaction = $event['data']['transaction'];
            echo "Payment processing: " . $transaction['reference_id'];
            break;

        case 'payment_intent.succeeded':
            // Handle successful payment
            $transaction = $event['data']['transaction'];
            echo "Payment completed: " . $transaction['reference_id'];
            break;

        case 'payment_intent.failed':
            // Handle failed payment
            $transaction = $event['data']['transaction'];
            echo "Payment failed: " . $transaction['reference_id'];
            break;
            
        case 'payment_intent.cancelled':
            // Handle cancelled payment
            $transaction = $event['data']['transaction'];
            echo "Payment cancelled: " . $transaction['reference_id'];
            break;
            
        case 'payment_intent.expired':
            // Handle expired payment
            $transaction = $event['data']['transaction'];
            echo "Payment expired: " . $transaction['reference_id'];
            break;

        case 'system.health_check':
            // Handle health check
            $message = $event['data']['message'];
            echo "Health check received: " . $message;
            break;
    }
} else {
    http_response_code(400);
    echo "Invalid signature";
}
?>
```

## Error Handling

```php
use AxiomePayments\Exception\AxiomePaymentsException;
use AxiomePayments\Exception\AuthenticationException;
use AxiomePayments\Exception\InvalidRequestException;
use AxiomePayments\Exception\ApiException;

try {
    $payment = $axiomepayments->payments->create([
        'title' => 'My NFT',
        'amount' => 99.99,
        'currency' => 'USD'
    ]);
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (InvalidRequestException $e) {
    echo "Invalid request: " . $e->getMessage();
} catch (ApiException $e) {
    echo "API error: " . $e->getMessage();
} catch (AxiomePaymentsException $e) {
    echo "AxiomePayments error: " . $e->getMessage();
}
```

## Testing

### Sandbox Environment

```php
$axiomepayments = new AxiomePayments([
    'api_key' => 'sandbox-api-key',
    'api_secret' => 'sandbox-api-secret', 
    'environment' => 'sandbox'
]);
```

### 🧪 Sandbox Testing Rules

- 💳 Only card payments work reliably - Google Pay/ApplePay usually won't work
- 👤 Enter any valid format for customer profile data
- 📱 Use any phone number (must be valid, but we don't send real OTP/SMS - use "1234" or any other code)
- 💳 Always use test card: `4242 4242 4242 4242` with a future expiry date

#### 🔑 CVV determines payment outcome:

| CVV | Result |
|-----|--------|
| 400 | ❌ General failure |
| 401 | 💳 Declined by card issuer |
| 402 | 📝 Incorrect card details |
| 403 | 📊 Transaction limits exceeded |
| 404 | 💰 Insufficient funds |
| 405 | 🔒 Incorrect CVV |
| 406 | 🗑️ Failed card validation (card deleted) |
| 407 | 🆘 Failed card validation (contact support) |
| ANY OTHER (e.g., 123) | ✅ SUCCESS |

## API Reference

### AxiomePayments Client

```php
$client = new AxiomePayments([
    'api_key' => 'your-api-key',
    'api_secret' => 'your-api-secret',
    'environment' => 'production', // 'sandbox' or 'production'
    'timeout' => 30 // Request timeout in seconds
]);
```

### Payments

### Create Payment

**Request Parameters:**

- `amount` (required): Payment amount (minimum 0.01)
- `currency` (required): Currency code - see [Get Available Currencies](#get-available-currencies) for the list of supported currencies. For NFT checkout and Onramp, payment is always charged in USD or EUR but prices can be displayed as any of the listed currencies!
- `title` (optional): Title of the payment link
- `description` (optional): Description of the payment
- `redirect_url` (optional): Success redirect URL (uses sales channel configuration if empty)
- `success_url` (optional): URL to redirect to after successful payment
- `cancel_url` (optional): URL to redirect to after cancelled payment
- `customer_details` (optional): Customer information object
  - `email` (optional): Customer's email address
  - `first_name` (optional): Customer's first name
  - `middle_name` (optional): Customer's middle name
  - `last_name` (optional): Customer's last name
  - `id` (optional): Customer's unique identifier
  - `phone` (optional): Customer's phone number
  - `date_of_birth` (optional): Customer's date of birth (YYYY-MM-DD format)
  - `country_of_residence` (optional): Customer's country code (ISO format, e.g., 'US', 'GB')
  - `state_of_residence` (optional): US state short code alpha-2 - Required if country_of_residence is 'US', has to be dropped from the payload for any other country
  - `card_country_code` (optional): Prefill card billing address country
  - `card_city` (optional): Prefill card billing address city
  - `card_state_code` (optional): Prefill card billing address state - US state short code alpha-2 - Required if country_of_residence is 'US', has to be dropped from the payload for any other country
  - `card_post_code` (optional): Prefill card billing address postal code
  - `card_street` (optional): Prefill card billing address street address
- `metadata` (optional): Use this to identify the customer or payment session (returned in webhooks and API responses)
- `custom_fields` (optional): Custom key-value pairs for additional data
- `expires_at` (optional): ISO 8601 timestamp when payment link expires
- `theme` (optional): UI theme customization
  - `color` (optional): Primary theme color (hex format, e.g., '#7014f4')
- `lang` (optional): Language code (en, es, fr, de, it, pt, ru, zh, ja, ko, tr, ka)
- `multiple_use` (optional): Boolean - if payment link is meant for one or multiple payments
- `cancel_on_first_fail` (optional): Boolean - when true, payment link fails permanently after first failed attempt (default: false)
- `is_price_dynamic` (optional): Boolean - when true, allows customers to set their own payment amount during checkout. The amount parameter becomes optional when this is enabled (default: false)
- `customer_commission_percentage` (optional): Commission percentage charged to customer

**Response - Payment Object:**

- `id`: Payment link ID
- `transaction_id`: Internal transaction ID (only present after payment is initiated)
- `title`: Payment title
- `description`: Payment description
- `reference_id`: Unique payment reference
- `payment_link_id`: Payment link ID (same as id in create response)
- `payment_url`: URL where customer can complete payment
- `flow_type`: Flow type (e.g., 'nft')
- `amount`: Amount in fiat currency
- `fiat_base_amount`: Base fiat amount (before fees)
- `fiat_total_amount`: Total fiat amount (including fees)
- `currency`: Fiat currency code
- `fiat_currency`: Fiat currency code (same as currency)
- `commodity`: Commodity type (e.g., 'USDT')
- `commodity_amount`: Amount of commodity to be purchased
- `status`: Payment status (pending, attempting, processing, completed, failed, expired, cancelled)
- `fail_reason`: Reason for payment failure (if failed)
- `created_at`: ISO 8601 timestamp when payment was created
- `updated_at`: ISO 8601 timestamp when payment was last updated
- `paid_at`: ISO 8601 timestamp when payment was completed (if completed)
- `expires_at`: ISO 8601 timestamp when payment expires
- `custom_fields`: Custom fields array
- `customer_commission_percentage`: Customer commission percentage
- `multiple_use`: Whether payment link can be used multiple times
- `customer_details`: Customer details object
- `metadata`: Metadata object
- `payment_method`: Payment method information with keys:
  - `card_id`: Card identifier
  - `card_brand`: Card brand (e.g., 'VISA', 'MASTERCARD')
  - `payment_type`: Payment type (e.g., '3ds_v2')
  - `processed_through`: Payment processor (e.g., 'safecharge')
- `blockchain_tx_hash`: Blockchain transaction hash for completed crypto transfers (string or null)

### Get Payment Status

Get the status of a payment by transaction UUID.

```php
$payment = $axiomepayments->payments->status('...');
```

Returns a Payment object with all the fields listed above.

### Get Payment Link Status

Get the status of a payment link by payment link UUID.

```php
$payment = $axiomepayments->payments->paymentLinkStatus('...');
```

Returns a Payment object with all the fields listed above.

### Get Available Currencies

Get all active processing currencies supported by the AxiomePayments API.

```php
// Get all currencies as Currency objects
$currencies = $axiomepayments->currencies->all();

foreach ($currencies as $currency) {
    echo $currency->getShortCode() . ': ' . $currency->getName();
    echo ' (' . $currency->getSymbol() . ')' . PHP_EOL;
    echo 'USD Value: ' . $currency->getCurrentUsdValue() . PHP_EOL;

    if ($currency->isProcessedViaAnotherCurrency()) {
        echo 'Processed via: ' . $currency->getProcessedViaCurrencyCode() . PHP_EOL;
    }
}

// Or get as arrays for easier manipulation
$currenciesArray = $axiomepayments->currencies->allAsArray();
```

**Response - Currency Object:**

- `short_code`: Currency code (e.g., 'USD', 'EUR', 'GBP')
- `name`: Currency full name (e.g., 'US Dollar')
- `symbol`: Currency symbol (e.g., '$', '€', '£')
- `current_usd_value`: Current USD exchange rate value
- `processed_via_currency_code`: Currency code this currency is processed via (null if processed directly)

**Currency Object Methods:**

- `getShortCode()`: Get the currency code
- `getName()`: Get the currency name
- `getSymbol()`: Get the currency symbol
- `getCurrentUsdValue()`: Get the current USD exchange rate value
- `getProcessedViaCurrencyCode()`: Get the currency code this currency is processed via
- `isProcessedViaAnotherCurrency()`: Check if this currency is processed via another currency

## Support

- **API Documentation**: [https://axiomepayments.com/api-documentation](https://axiomepayments.com/api-documentation) (also available as [OpenAPI spec](https://docs.axiomepayments.com/))
- **Service Availability**: [https://axiomepayments.com/api-documentation/service-availability](https://axiomepayments.com/api-documentation/service-availability)
- **Telegram**: @AxiomePayments_Support_Bot
- **Email**: tech@axiomepayments.com

## Laravel Integration

### Installation

The SDK includes built-in Laravel support. After installing via Composer, add the service provider to your `config/app.php` providers array:

```php
'providers' => [
    // ...
    AxiomePayments\Laravel\AxiomePaymentsServiceProvider::class,
],
```

Add the facade to your aliases array:

```php
'aliases' => [
    // ...
    'AxiomePayments' => AxiomePayments\Laravel\Facades\AxiomePayments::class,
],
```

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="AxiomePayments\Laravel\AxiomePaymentsServiceProvider" --tag="axiomepayments-config"
```

This will create a `config/axiomepayments.php` file. You can also use environment variables in your `.env` file:

```env
AXIOMEPAYMENTS_API_KEY=your-api-key
AXIOMEPAYMENTS_API_SECRET=your-api-secret
AXIOMEPAYMENTS_ENVIRONMENT=sandbox
AXIOMEPAYMENTS_API_URL=https://custom-api.axiomepayments.com/v1.0  # Optional
```

### Usage with Laravel

Using the Facade:

```php
use AxiomePayments\Laravel\Facades\AxiomePayments;

// Create a payment
$payment = AxiomePayments::payments->create([
    'amount' => 99.99,
    'currency' => 'USD',
]);

// Check payment status
$status = AxiomePayments::payments->status('payment_id');

// List payments
$payments = AxiomePayments::payments->list([
    'limit' => 10,
    'status' => 'completed'
]);
```

Using Dependency Injection:

```php
use AxiomePayments\AxiomePayments;

class PaymentController extends Controller
{
    public function store(Request $request, AxiomePayments $axiomepayments)
    {
        $payment = $axiomepayments->payments->create([
            'amount' => $request->amount,
            'currency' => 'USD',
        ]);

        return redirect($payment->payment_url);
    }
}
```

### Laravel Webhook Handling

Create a webhook controller:

```php
use Illuminate\Http\Request;
use AxiomePayments\Laravel\Facades\AxiomePayments;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('X-Webhook-Signature');

        if (!$this->verifySignature($payload, $signature)) {
            abort(400, 'Invalid signature');
        }

        switch ($payload['type']) {
            case 'payment_intent.succeeded':
                // Handle successful payment
                $payment = $payload['data'];
                event(new PaymentCompletedEvent($payment));
                break;

            //...

            case 'payment_intent.failed':
                // Handle failed payment
                event(new PaymentFailedEvent($payload['data']));
                break;
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
```

### Testing in Laravel

The SDK includes Laravel-specific test helpers. In your tests:

```php
use AxiomePayments\Laravel\Facades\AxiomePayments;

class PaymentTest extends TestCase
{
    public function test_can_create_payment()
    {
        $payment = AxiomePayments::payments->create([
            'amount' => 99.99,
            'currency' => 'USD',
        ]);

        $this->assertNotNull($payment->id);
        $this->assertEquals(99.99, $payment->amount);
        $this->assertEquals('USD', $payment->currency);
    }
}
```

## License

This SDK is released under the MIT License. See [LICENSE](LICENSE) for details. 