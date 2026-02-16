<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function findAllOrderedByCode(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Subject $subject, bool $flush = false): void
    {
        $this->getEntityManager()->persist($subject);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subject $subject, bool $flush = false): void
    {
        $this->getEntityManager()->remove($subject);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCode(string $code): ?Subject
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('s.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithoutTeacher(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.teacher IS NULL')
            ->orderBy('s.code', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
