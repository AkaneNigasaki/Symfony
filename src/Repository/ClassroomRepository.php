<?php

namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClassroomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classroom::class);
    }

    /**
     * @param list<School> $schools
     * @return list<Classroom>
     */
    public function findBySchools(array $schools, ?int $schoolId = null): array
    {
        if (empty($schools)) {
            return [];
        }
        $qb = $this->createQueryBuilder('c')
            ->where('c.school IN (:schools)')
            ->setParameter('schools', $schools)
            ->orderBy('c.name', 'ASC');
        if ($schoolId !== null) {
            $qb->andWhere('c.school = :schoolId')->setParameter('schoolId', $schoolId);
        }
        return $qb->getQuery()->getResult();
    }

    public function save(Classroom $classroom, bool $flush = false): void
    {
        $this->getEntityManager()->persist($classroom);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Classroom $classroom, bool $flush = false): void
    {
        $this->getEntityManager()->remove($classroom);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAvailableByCapacity(int $minCapacity): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.capacity >= :minCapacity')
            ->setParameter('minCapacity', $minCapacity)
            ->orderBy('c.capacity', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
