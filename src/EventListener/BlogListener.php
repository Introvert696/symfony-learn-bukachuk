<?php
// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

// ...
use App\Entity\Blog;
use App\Message\ContentWatchJob;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class BlogListener
{
    private array $entitys;
    public function __construct(private MessageBusInterface $bus){

    }
      public function postFlush(PostFlushEventArgs $event): void
    {
        foreach( $this->entitys as $blog){
            $this->bus->dispatch(new ContentWatchJob($blog->getId()));
        }

    }
    public function postPersist(PostPersistEventArgs $event){
        if($event->getObject() instanceof Blog){
            $this->entitys[] = $event->getObject();
        }
    }
}