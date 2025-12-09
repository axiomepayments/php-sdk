<?php

namespace AxiomePayments\Service;

use AxiomePayments\Http\Client;
use AxiomePayments\Model\Payment;
use AxiomePayments\Model\PaymentList;
use AxiomePayments\Exception\InvalidRequestException;
use AxiomePayments\Exception\AxiomePaymentsException;

/**
 * Payment service for handling payment operations
 */
class PaymentService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Create a new PaymentService instance
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new payment
     *
     * @param array $params Payment parameters
     * @return Payment
     * @throws AxiomePaymentsException
     */
    public function create(array $params): Payment
    {
        $this->validateCreateParams($params);
        
        $response = $this->client->post('/payment/create', $params);
        
        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return Payment::fromArray($response['data']);
    }

    /**
     * Get payment status by transaction ID
     *
     * @param string $referenceId Payment transaction ID
     * @return Payment
     * @throws AxiomePaymentsException
     */
    public function status(string $transactionId): Payment
    {
        if (empty($transactionId)) {
            throw new InvalidRequestException('Transaction ID is required');
        }

        $response = $this->client->get("/payment/status/{$transactionId}");

        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return Payment::fromArray($response['data']);
    }

    /**
     * Get payment link status by payment link ID
     *
     * @param string $paymentLinkId Payment link ID
     * @return Payment
     * @throws AxiomePaymentsException
     */
    public function paymentLinkStatus(string $paymentLinkId): Payment
    {
        if (empty($paymentLinkId)) {
            throw new InvalidRequestException('Payment link ID is required');
        }

        $response = $this->client->get("/payment-link/status/{$paymentLinkId}");

        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return Payment::fromArray($response['data']);
    }

    /**
     * List payments with optional filtering and pagination
     *
     * @param array $params Query parameters
     * @return PaymentList
     * @throws AxiomePaymentsException
     */
    public function list(array $params = []): PaymentList
    {
        $this->validateListParams($params);
        
        $response = $this->client->get('/payment/list', $params);
        
        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return PaymentList::fromArray($response['data']);
    }

    /**
     * Validate parameters for creating a payment
     *
     * @param array $params
     * @throws InvalidRequestException
     */
    private function validateCreateParams(array $params): void
    {
        // Check if dynamic pricing is enabled
        $isPriceDynamic = isset($params['is_price_dynamic']) && $params['is_price_dynamic'] === true;

        // Validate is_price_dynamic if provided
        if (isset($params['is_price_dynamic']) && !is_bool($params['is_price_dynamic'])) {
            throw new InvalidRequestException('is_price_dynamic must be a boolean');
        }

        // Amount is required unless dynamic pricing is enabled
        if (!$isPriceDynamic) {
            if (!isset($params['amount'])) {
                throw new InvalidRequestException('Amount is required');
            }

            $amount = $params['amount'];
            if (!is_numeric($amount) || $amount < 0.01) {
                throw new InvalidRequestException('Amount must be a number greater than or equal to 0.01');
            }
        } elseif (isset($params['amount'])) {
            // If amount is provided with dynamic pricing, validate it anyway
            $amount = $params['amount'];
            if (!is_numeric($amount) || $amount < 0.01) {
                throw new InvalidRequestException('Amount must be a number greater than or equal to 0.01');
            }
        }

        // Validate currency if provided - just check it's a non-empty string
        // The API will validate against active currencies
        if (isset($params['currency'])) {
            if (!is_string($params['currency']) || empty(trim($params['currency']))) {
                throw new InvalidRequestException('Currency must be a valid currency code');
            }
        }

        // Validate URLs if provided
        if (isset($params['redirect_url']) && !filter_var($params['redirect_url'], FILTER_VALIDATE_URL)) {
            throw new InvalidRequestException('Invalid redirect URL');
        }
         // Validate URLs if provided
         if (isset($params['success_url']) && !filter_var($params['success_url'], FILTER_VALIDATE_URL)) {
            throw new InvalidRequestException('Invalid success URL');
        }
         // Validate URLs if provided
         if (isset($params['cancel_url']) && !filter_var($params['cancel_url'], FILTER_VALIDATE_URL)) {
            throw new InvalidRequestException('Invalid cancel URL');
        }

        // Validate language if provided
        if (isset($params['lang'])) {
            $validLanguages = ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ko', 'tr', 'ka'];
            if (!in_array($params['lang'], $validLanguages)) {
                throw new InvalidRequestException('Language must be one of: ' . implode(', ', $validLanguages));
            }
        }

        // Validate title length if provided
        if (isset($params['title']) && strlen($params['title']) > 255) {
            throw new InvalidRequestException('Title must not exceed 255 characters');
        }

        // Validate description length if provided
        if (isset($params['description']) && strlen($params['description']) > 1000) {
            throw new InvalidRequestException('Description must not exceed 1000 characters');
        }

        // Validate expiration date if provided
        if (isset($params['expires_at'])) {
            $expiresAt = strtotime($params['expires_at']);
            if ($expiresAt === false || $expiresAt <= time()) {
                throw new InvalidRequestException('Expiration date must be in the future');
            }
        }

        // Validate custom fields if provided
        if (isset($params['custom_fields']) && !is_array($params['custom_fields'])) {
            throw new InvalidRequestException('Custom fields must be an array');
        }

        // Validate email if provided
        if (isset($params['customer_email']) && !filter_var($params['customer_email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidRequestException('Invalid email address');
        }
        // Validate email if provided
        if (isset($params['customer_details']['email']) && !filter_var($params['customer_details']['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidRequestException('Invalid email address');
        }
    }

    /**
     * Validate parameters for listing payments
     *
     * @param array $params
     * @throws InvalidRequestException
     */
    private function validateListParams(array $params): void
    {
        // Validate limit
        if (isset($params['limit'])) {
            $limit = $params['limit'];
            if (!is_int($limit) || $limit < 1 || $limit > 100) {
                throw new InvalidRequestException('Limit must be an integer between 1 and 100');
            }
        }

        // Validate status
        if (isset($params['status'])) {
            $validStatuses = ['pending', 'completed', 'failed', 'expired'];
            if (!in_array($params['status'], $validStatuses)) {
                throw new InvalidRequestException('Status must be one of: ' . implode(', ', $validStatuses));
            }
        }

        // Validate dates
        if (isset($params['from_date'])) {
            if (!$this->isValidDate($params['from_date'])) {
                throw new InvalidRequestException('from_date must be in YYYY-MM-DD format');
            }
        }

        if (isset($params['to_date'])) {
            if (!$this->isValidDate($params['to_date'])) {
                throw new InvalidRequestException('to_date must be in YYYY-MM-DD format');
            }
        }

        // Validate date range
        if (isset($params['from_date']) && isset($params['to_date'])) {
            if (strtotime($params['from_date']) > strtotime($params['to_date'])) {
                throw new InvalidRequestException('from_date must be before or equal to to_date');
            }
        }
    }

    /**
     * Get conversion rate for a network, commodity, and fiat currency
     *
     * @param string $network Network (e.g., 'POL', 'BSC')
     * @param string $commodity Commodity (e.g., 'USDT')
     * @param string $fiatCurrency Fiat currency code (e.g., 'USD', 'EUR')
     * @return array Conversion rate data
     * @throws AxiomePaymentsException
     */
    public function getConversionRate(string $network, string $commodity, string $fiatCurrency, string $paymentMethod = 'card'): array
    {
        $this->validateConversionRateParams($network, $commodity, $fiatCurrency);

        $response = $this->client->get("/conversion-rate/{$network}/{$commodity}/{$fiatCurrency}/{$paymentMethod}");

        if (!isset($response['data'])) {
            throw new AxiomePaymentsException('Invalid response format from API');
        }

        return $response['data'];
    }

    /**
     * Validate parameters for getting conversion rate
     *
     * @param string $network
     * @param string $commodity
     * @param string $fiatCurrency
     * @throws InvalidRequestException
     */
    private function validateConversionRateParams(string $network, string $commodity, string $fiatCurrency): void
    {
        // Basic validation - API will validate against active networks/commodities/currencies
        if (empty(trim($network))) {
            throw new InvalidRequestException('Network is required');
        }

        if (empty(trim($commodity))) {
            throw new InvalidRequestException('Commodity is required');
        }

        if (empty(trim($fiatCurrency))) {
            throw new InvalidRequestException('Fiat currency is required');
        }
    }

    /**
     * Check if a date string is valid (YYYY-MM-DD format)
     *
     * @param string $date
     * @return bool
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
} 