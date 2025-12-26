<?php

namespace AxiomePayments\Tests;

use PHPUnit\Framework\TestCase;
use AxiomePayments\Webhook;
use AxiomePayments\Exception\AxiomePaymentsException;

class WebhookTest extends TestCase
{
    private $secret = 'test-secret';
    private $webhook;

    protected function setUp(): void
    {
        $this->webhook = new Webhook($this->secret);
    }

    public function testVerifySignature()
    {
        $payload = json_encode(['event' => 'test']);
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $this->secret);
        
        $this->assertTrue($this->webhook->verifySignature($payload, $signature));
    }

    public function testParseSystemHealthCheckEvent()
    {
        $eventData = [
            'event' => 'system.health_check',
            'timestamp' => date('c'),
            'data' => [
                'message' => 'Health check',
                'sales_channel_id' => 123
            ]
        ];
        $payload = json_encode($eventData);
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $this->secret);

        $result = $this->webhook->verifyAndParse($payload, $signature);

        $this->assertEquals('system.health_check', $result['event']);
        $this->assertEquals('Health check', $result['data']['message']);
        $this->assertEquals(123, $result['data']['sales_channel_id']);
        
        $this->assertTrue($this->webhook->isSystemHealthCheck($result));
    }

    public function testIdentifyPaymentIntentEvents()
    {
        $events = [
            'payment_intent.created' => 'isPaymentIntentCreated',
            'payment_intent.attempting' => 'isPaymentIntentAttempting',
            'payment_intent.processing' => 'isPaymentIntentProcessing',
            'payment_intent.succeeded' => 'isPaymentIntentSucceeded',
            'payment_intent.failed' => 'isPaymentIntentFailed',
            'payment_intent.cancelled' => 'isPaymentIntentCancelled',
            'payment_intent.expired' => 'isPaymentIntentExpired',
        ];

        foreach ($events as $eventType => $method) {
            $payload = ['event' => $eventType];
            $this->assertTrue($this->webhook->$method($payload), "Method $method should return true for $eventType");
        }
    }
}
