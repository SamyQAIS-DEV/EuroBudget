<?php

namespace App\Tests\Service;

use App\Service\MailerService;
use App\Tests\KernelTestCase;

class MailerServiceTest extends KernelTestCase
{
    private MailerService $mailer;

    public function setUp(): void
    {
        parent::setUp();
        $this->mailer = self::getContainer()->get(MailerService::class);
    }

    public function testSubject(): void
    {
        $email = $this->mailer->createEmail('mails/base.html.twig', 'Votre lien de connexion !', [])
            ->to('test@email.com');
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }
}