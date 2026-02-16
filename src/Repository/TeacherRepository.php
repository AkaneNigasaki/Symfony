<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.lastName', 'ASC')
            ->addOrderBy('t.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Teacher $teacher, bool $flush = false): void
    {
        $this->getEntityManager()->persist($teacher);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Teacher $teacher, bool $flush = false): void
    {
        $this->getEntityManager()->remove($teacher);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByEmail(string $email): ?Teacher
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBySpecialization(string $specialization): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.specialization = :specialization')
            ->setParameter('specialization', $specialization)
            ->orderBy('t.lastName', 'ASC')
            ->addOrderBy('t.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithSubjects(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.subjects', 's')
            ->addSelect('s')
            ->orderBy('t.lastName', 'ASC')
            ->addOrderBy('t.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
