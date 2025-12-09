<?php

namespace AxiomePayments\Tests\Model;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Model\Payment;

class PaymentTest extends TestCase
{
    private array $paymentData = [
        'id' => 1,
        'transaction_id' => 123,
        'title' => 'Test Payment',
        'description' => 'Test payment description',
        'reference_id' => 'ref_123',
        'payment_url' => 'https://pay.example.com/xyz',
        'amount' => 100.00,
        'currency' => 'USD',
        'status' => 'pending',
        'expires_at' => '2025-08-07T10:00:00Z',
        'custom_fields' => ['field1' => 'value1'],
        'customer_commission_percentage' => 2.5,
        'created_at' => '2025-08-06T10:00:00Z',
        'updated_at' => '2025-08-06T10:00:00Z',
        'customer_details' => [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ],
        'metadata' => [
            'order_id' => '12345'
        ],
        'payment_method' => [
            'card_brand' => 'VISA',
            'payment_type' => 'card',
            'processed_through' => 'safecharge',
        ],
        'multiple_use' => true,
        'blockchain_tx_hash' => '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef',
    ];

    public function testCanCreateFromArray()
    {
        $payment = Payment::fromArray($this->paymentData);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(1, $payment->id);
        $this->assertEquals(123, $payment->transaction_id);
        $this->assertEquals('Test Payment', $payment->title);
        $this->assertEquals('Test payment description', $payment->description);
        $this->assertEquals('ref_123', $payment->reference_id);
        $this->assertEquals('https://pay.example.com/xyz', $payment->payment_url);
        $this->assertEquals(100.00, $payment->amount);
        $this->assertEquals('USD', $payment->currency);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('2025-08-07T10:00:00Z', $payment->expires_at);
        $this->assertEquals(['field1' => 'value1'], $payment->custom_fields);
        $this->assertEquals(2.5, $payment->customer_commission_percentage);
        $this->assertEquals('2025-08-06T10:00:00Z', $payment->created_at);
        $this->assertEquals('2025-08-06T10:00:00Z', $payment->updated_at);
        $this->assertEquals(['email' => 'test@example.com', 'first_name' => 'Test', 'last_name' => 'User'], $payment->customer_details);
        $this->assertEquals(['order_id' => '12345'], $payment->metadata);
        $this->assertEquals(['card_brand' => 'VISA', 'payment_type' => 'card', 'processed_through' => 'safecharge'], $payment->payment_method);
        $this->assertEquals(true, $payment->multiple_use);
        $this->assertEquals('0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef', $payment->blockchain_tx_hash);
    }

    public function testCanConvertToArray()
    {
        $payment = Payment::fromArray($this->paymentData);
        $array = $payment->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($this->paymentData, $array);
    }

    public function testToArrayOmitsNullValues()
    {
        $payment = new Payment();
        $payment->id = 1;
        $payment->amount = 100.00;
        // status is null

        $array = $payment->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayNotHasKey('status', $array);
    }

    public function testGettersReturnCorrectValues()
    {
        $payment = Payment::fromArray($this->paymentData);

        $this->assertEquals($this->paymentData['id'], $payment->getId());
        $this->assertEquals($this->paymentData['amount'], $payment->getAmount());
        $this->assertEquals($this->paymentData['currency'], $payment->getCurrency());
        $this->assertEquals($this->paymentData['status'], $payment->getStatus());
        $this->assertEquals($this->paymentData['blockchain_tx_hash'], $payment->getBlockchainTxHash());
    }

    public function testStatusCheckers()
    {
        // Test pending status
        $payment = new Payment();
        $payment->status = 'pending';
        
        $this->assertTrue($payment->isPending());
        $this->assertFalse($payment->isCompleted());
        $this->assertFalse($payment->isFailed());
        $this->assertFalse($payment->isExpired());

        // Test completed status
        $payment->status = 'completed';
        
        $this->assertFalse($payment->isPending());
        $this->assertTrue($payment->isCompleted());
        $this->assertFalse($payment->isFailed());
        $this->assertFalse($payment->isExpired());

        // Test failed status
        $payment->status = 'failed';
        
        $this->assertFalse($payment->isPending());
        $this->assertFalse($payment->isCompleted());
        $this->assertTrue($payment->isFailed());
        $this->assertFalse($payment->isExpired());

        // Test expired status
        $payment->status = 'expired';
        
        $this->assertFalse($payment->isPending());
        $this->assertFalse($payment->isCompleted());
        $this->assertFalse($payment->isFailed());
        $this->assertTrue($payment->isExpired());
    }

    public function testHandlesNullPaymentData()
    {
        $payment = Payment::fromArray([]);

        $this->assertNull($payment->id);
        $this->assertNull($payment->transaction_id);
        $this->assertNull($payment->title);
        $this->assertNull($payment->amount);
        $this->assertNull($payment->status);
    }

    public function testHandlesPartialPaymentData()
    {
        $partialData = [
            'id' => 1,
            'amount' => 100.00,
            // Other fields missing
        ];

        $payment = Payment::fromArray($partialData);

        $this->assertEquals(1, $payment->id);
        $this->assertEquals(100.00, $payment->amount);
        $this->assertNull($payment->status);
        $this->assertNull($payment->currency);
    }

    public function testHandlesNumericalTypeConversion()
    {
        $data = [
            'amount' => '100.00', // String amount
            'customer_commission_percentage' => '2.5' // String percentage
        ];

        $payment = Payment::fromArray($data);

        $this->assertIsFloat($payment->amount);
        $this->assertEquals(100.00, $payment->amount);
        $this->assertIsFloat($payment->customer_commission_percentage);
        $this->assertEquals(2.5, $payment->customer_commission_percentage);
    }
}