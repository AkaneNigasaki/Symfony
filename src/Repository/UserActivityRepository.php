<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserActivity>
 */
class UserActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserActivity::class);
    }

    /**
     * @return UserActivity[] Returns an array of UserActivity objects
     */
    public function findRecentByUser(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->setParameter('user', $user)
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
