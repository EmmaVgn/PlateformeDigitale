<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSlugSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['handleEntityEvents', 0],
            BeforeEntityUpdatedEvent::class => ['handleEntityEvents', 0],
        ];
    }

    public function handleEntityEvents(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        // Gestion des Slugs
        if (method_exists($entity, 'getName') && method_exists($entity, 'setSlug')) {
            $name = $entity->getName();
            if ($name) {
                $slug = $this->slugger->slug($name)->lower();
                $formattedName = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
                $entity->setSlug($slug)->setName($formattedName);
            }
        }

        // Gestion des mots de passe (User)
        if ($entity instanceof User && !empty($entity->getPlainPassword())) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPlainPassword());
            $entity->setPassword($hashedPassword);
        }
    }
}
