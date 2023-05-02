<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private const MAINTENANCE_FILE_PATH = '/public/.maintenance';

    public function __construct(private readonly Environment $twig, private readonly string $projectDir)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!file_exists($this->projectDir . self::MAINTENANCE_FILE_PATH)) {
            return;
        }
        $event->setResponse(
            new Response($this->twig->render('maintenance/maintenance.html.twig'), Response::HTTP_SERVICE_UNAVAILABLE)
        );
        $event->stopPropagation();
    }
}
