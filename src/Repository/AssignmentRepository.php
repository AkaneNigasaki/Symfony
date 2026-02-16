<?php

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function save(Assignment $assignment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($assignment);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Assignment $assignment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($assignment);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCourse(int $courseId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('a.dueDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcoming(\DateTimeImmutable $from = null): array
    {
        $from = $from ?? new \DateTimeImmutable();
        
        return $this->createQueryBuilder('a')
            ->andWhere('a.dueDate >= :from')
            ->setParameter('from', $from)
            ->orderBy('a.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOverdue(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dueDate < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('a.dueDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
