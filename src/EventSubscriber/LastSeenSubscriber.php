<?php

namespace App\EventSubscriber;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class LastSeenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof Utilisateur) {
            return;
        }

        $now = new \DateTimeImmutable();
        $lastSeen = $user->getLastSeenAt();
        if ($lastSeen !== null && $lastSeen->getTimestamp() > ($now->getTimestamp() - 60)) {
            return;
        }

        $user->setLastSeenAt($now);
        $this->entityManager->flush();
    }
}
