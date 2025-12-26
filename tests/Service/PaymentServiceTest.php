<?php

namespace AxiomePayments\Tests\Service;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Service\PaymentService;
use AxiomePayments\Http\Client;
use AxiomePayments\Model\Payment;
use AxiomePayments\Model\PaymentList;
use AxiomePayments\Exception\InvalidRequestException;

class PaymentServiceTest extends TestCase
{
    private $client;
    private $paymentService;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->paymentService = new PaymentService($this->client);
    }

    public function testCreatePayment()
    {
        $params = [
            'amount' => 100.0,
            'currency' => 'USD'
        ];

        $responseData = [
            'data' => [
                'id' => 'pay_123',
                'amount' => 100.0,
                'currency' => 'USD',
                'status' => 'pending'
            ]
        ];

        $this->client->expects($this->once())
            ->method('post')
            ->with('/payment/create', $params)
            ->willReturn($responseData);

        $payment = $this->paymentService->create($params);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('pay_123', $payment->id);
    }

    public function testGetPaymentStatus()
    {
        $transactionId = 'tx_123';
        $responseData = [
            'data' => [
                'id' => 'pay_123',
                'transaction_id' => $transactionId,
                'status' => 'completed'
            ]
        ];

        $this->client->expects($this->once())
            ->method('get')
            ->with("/payment/status/{$transactionId}")
            ->willReturn($responseData);

        $payment = $this->paymentService->status($transactionId);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('completed', $payment->status);
    }

    public function testListPayments()
    {
        $params = ['limit' => 10];
        $responseData = [
            'data' => [
                'payments' => [
                    ['id' => 'pay_1', 'status' => 'completed'],
                    ['id' => 'pay_2', 'status' => 'pending']
                ],
                'pagination' => [
                    'total' => 2,
                    'count' => 2,
                    'per_page' => 10,
                    'current_page' => 1,
                    'total_pages' => 1
                ]
            ]
        ];

        $this->client->expects($this->once())
            ->method('get')
            ->with('/payment/list', $params)
            ->willReturn($responseData);

        $paymentList = $this->paymentService->list($params);

        $this->assertInstanceOf(PaymentList::class, $paymentList);
        $this->assertCount(2, $paymentList->payments);
    }

    public function testValidateCreateParamsThrowsExceptionForMissingAmount()
    {
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('Amount is required');

        $this->paymentService->create(['currency' => 'USD']);
    }

    public function testValidateCreateParamsThrowsExceptionForInvalidCurrency()
    {
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('Currency must be a valid currency code');

        $this->paymentService->create([
            'amount' => 100,
            'currency' => ''
        ]);
    }
}