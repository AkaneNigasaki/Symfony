<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserActivity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ActivityLogger
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function log(string $description, string $icon = 'activity', ?User $user = null): void
    {
        $user = $user ?? $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $activity = new UserActivity();
        $activity->setUser($user);
        $activity->setDescription($description);
        $activity->setIcon($icon);

        $this->entityManager->persist($activity);
        $this->entityManager->flush();
    }
}
