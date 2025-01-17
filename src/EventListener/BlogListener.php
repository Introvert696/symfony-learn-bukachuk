<?php
// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

// ...
use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
class BlogListener
{
    public function __construct(private MessageBusInterface $bus){

    }
      public function postFlush(PostFlushEventArgs $event): void
    {
        dd($event);

    }
}