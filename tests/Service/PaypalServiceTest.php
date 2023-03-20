<?php

namespace App\Tests\Service;

use App\Service\PaypalService;
use App\Tests\KernelTestCase;

class PaypalServiceTest extends KernelTestCase
{
    private PaypalService $paypalService;

    public function setUp(): void
    {
        parent::setUp();
        $this->paypalService = self::getContainer()->get(PaypalService::class);
    }

    public function testSubject(): void
    {
        $payment = $this->paypalService->createPayment(1);
        dd($payment);
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }
}