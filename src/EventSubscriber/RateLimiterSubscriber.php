<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private HttpKernelInterface $httpKernel, 
        private RateLimiterFactory $apiLimiter
    ){}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $limiter = $this->apiLimiter->create($request->getClientIp());
        $limit = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            $event->setResponse(new JsonResponse(['error' => 'Rate limit exceeded. Please try again later.'], 429));
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}