<?php

namespace App\Repository;

use App\Entity\School;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    public function save(School $school, bool $flush = false): void
    {
        $this->getEntityManager()->persist($school);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(School $school, bool $flush = false): void
    {
        $this->getEntityManager()->remove($school);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithStats(int $schoolId): ?School
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.classrooms', 'c')
            ->leftJoin('s.teachers', 't')
            ->leftJoin('s.courses', 'co')
            ->leftJoin('s.students', 'st')
            ->addSelect('c', 't', 'co', 'st')
            ->where('s.id = :schoolId')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByOwnerOrderedByName(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByIdAndOwner(int $id, User $user): ?School
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->andWhere('s.owner = :owner')
            ->setParameter('id', $id)
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByIdAndUser(int $id, User $user): ?School
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.students', 'st', 'WITH', 'st.user = :user AND st.status = :status')
            ->where('s.id = :id')
            ->andWhere('s.owner = :user OR st.id IS NOT NULL')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
