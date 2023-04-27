<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    public function __construct(
        private readonly Environment $twig,
        private readonly MailerInterface $mailer,
        private readonly string $mailPrefixSubject,
        private readonly string $senderAddress,
        private readonly string $senderName,
    ) {
    }

    public function createEmail(string $template, string $subject, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from(new Address($this->senderAddress, $this->senderName))
            ->html($html)
            ->text($text)
            ->subject($this->mailPrefixSubject . ' | ' . $subject);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email): void
    {
        $this->mailer->send($email);
    }
}
