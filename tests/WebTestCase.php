<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    use FixturesTrait;
    use PHPUnitTrait;

    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->em->clear();
        parent::tearDown();
    }

    public function jsonRequest(string $method, string $url, ?array $data = null): string
    {
        $this->client->request($method, $url, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $data ? json_encode($data, JSON_THROW_ON_ERROR) : null);

        return $this->client->getResponse()->getContent();
    }

    /**
     * Vérifie si on a un message de succès.
     */
    public function expectAlert(string $type, ?string $message = null, int $count = 1): void
    {
        $this->assertEquals($count, $this->client->getCrawler()->filter('.alerts alert-element[type="' . $type . '"]')->count());
        if ($message) {
            $this->assertStringContainsString($message, $this->client->getCrawler()->filter('.alerts alert-element[type="' . $type . '"]')->text());
        }
    }

    /**
     * Vérifie si on a un message d'erreur via le texte.
     */
    public function expectErrorAlertMessage(string $message): void
    {
        $this->assertStringContainsString($message, $this->client->getCrawler()->filter('alerts alert-element[type="danger"], .alerts alert-element[type="error"]')->text());
    }

    /**
     * Vérifie si on a un message de succès.
     */
    public function expectSuccessAlert(?string $message = null): void
    {
        $this->expectAlert('success', $message);
    }

    /**
     * Vérifie si on a un message d'erreur.
     */
    public function expectErrorAlert(?string $message = null): void
    {
        $this->expectAlert('error', $message);
    }

    public function expectFormErrors(?int $expectedErrors = null): void
    {
        if (null === $expectedErrors) {
            $this->assertTrue($this->client->getCrawler()->filter('.form-error')->count() > 0, 'Form errors missmatch.');
        } else {
            $this->assertEquals($expectedErrors, $this->client->getCrawler()->filter('.form-error')->count(), 'Form errors missmatch.');
        }
    }

    public function expectH1(string $title): void
    {
        $crawler = $this->client->getCrawler();
        $this->assertEquals(
            $title,
            $crawler->filter('h1')->text(),
            '<h1> missmatch'
        );
    }

    public function expectTitle(string $title): void
    {
        $crawler = $this->client->getCrawler();
        $this->assertEquals(
            $title.' | SamyQais',
            $crawler->filter('title')->text(),
            '<title> missmatch',
        );
    }

    public function login(?User $user)
    {
        if (null === $user) {
            return;
        }
        $this->client->loginUser($user);
    }

    public function setCsrf(string $key): string
    {
        $csrf = uniqid();
        self::getContainer()->get(TokenStorageInterface::class)->setToken($key, $csrf);

        return $csrf;
    }

    protected function getRequest(): Request
    {
        $this->ensureSessionIsAvailable();
        $this->client->request('GET', '/contact');
        return $this->client->getRequest();
    }

    protected function getSession(): SessionInterface
    {
        $this->ensureSessionIsAvailable();
        $this->client->request('GET', '/contact');
        return $this->client->getRequest()->getSession();
    }

    private function ensureSessionIsAvailable(): void
    {
        $container = self::getContainer();
        $requestStack = $container->get('request_stack');

        try {
            $requestStack->getSession();
        } catch (SessionNotFoundException) {
            $session = $container->has('session')
                ? $container->get('session')
                : $container->get('session.factory')->createSession();

            $masterRequest = new Request();
            $masterRequest->setSession($session);

            $requestStack->push($masterRequest);

            $session->start();
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
        }
    }
}
