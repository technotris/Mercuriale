<?php

namespace App\FileImport\Application\EventSubscriber;

use App\FileImport\Domain\Entity\FileImport;
use App\FileImport\Application\Message\FileImportNotification;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

// Possible issue if upload is not done on the easyadmin interface
class FileImportSubscriber implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['sendMessageNewDraft'],
        ];
    }

    public function sendMessageNewDraft(AfterEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof FileImport)) {
            return;
        }

        $this->bus->dispatch(new FileImportNotification($entity->getId()));
    }
}
