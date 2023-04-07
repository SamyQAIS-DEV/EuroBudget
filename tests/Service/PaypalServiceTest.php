<?php

namespace App\Tests\Service;

use App\Entity\Payment;
use App\Exception\PaymentFailedException;
use App\Service\PaypalService;
use App\Tests\KernelTestCase;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalHttp\HttpResponse;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class PaypalServiceTest extends KernelTestCase
{
    public function testCreatePayment(): void
    {
        $response = new HttpResponse(Response::HTTP_OK, $this->getOrder(), []);
        $client = $this->createMock(PayPalHttpClient::class);
        $client->expects($this->once())->method('execute')->willReturn($response);
        $this->paypalService = new PaypalService($client);

        $payment = $this->paypalService->createPayment(1);
        $this->assertInstanceOf(Payment::class, $payment);
    }

    public function testCapture(): void
    {
        $response = new HttpResponse(Response::HTTP_OK, $this->getCapture(), []);
        $client = $this->createMock(PayPalHttpClient::class);
        $client->expects($this->once())->method('execute')->willReturn($response);
        $this->paypalService = new PaypalService($client);

        $payment = new Payment();
        $payment->id = '123';
        $payment = $this->paypalService->capture($payment);
        $this->assertInstanceOf(Payment::class, $payment);
    }

    public function testCaptureNotCompleted(): void
    {
        $capture = $this->getCapture();
        $capture->status = 'NOT COMPLETED';
        $response = new HttpResponse(Response::HTTP_OK, $capture, []);
        $client = $this->createMock(PayPalHttpClient::class);
        $client->expects($this->once())->method('execute')->willReturn($response);
        $this->paypalService = new PaypalService($client);

        $payment = new Payment();
        $payment->id = '123';
        $this->expectException(PaymentFailedException::class);
        $payment = $this->paypalService->capture($payment);
    }

    private function getOrder(): stdClass
    {
        return (object) [
            'id' => 123,
            'payer' => (object) [
                'name' => (object) [
                    'given_name' => 'Test given_name',
                    'surname' => 'Test surname'
                ],
            ],
            'purchase_units' => [
                (object) [
                    'custom_id' => 123,
                    'shipping' => (object) [
                        'address' => (object) [
                            'address_line_1' => 'Test address_line_1',
                            'admin_area_2' => 'Test admin_area_2',
                            'postal_code' => 'Test postal_code',
                            'country_code' => 'Test country_code',
                        ]
                    ],
                    'amount' => (object) [
                        'value' => 123,
                        'breakdown' => (object) [
                            'tax_total' => (object) [
                                'value' => 0.2
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function getCapture(): stdClass
    {
        return (object) [
            'status' => 'COMPLETED',
            'purchase_units' => [
                (object) [
                    'payments' => (object) [
                        'captures' => [
                            (object) [
                                'id' => 123,
                                'seller_receivable_breakdown' => (object) [
                                    'paypal_fee' => (object) [
                                        'value' => 0.3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}