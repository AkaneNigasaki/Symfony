<?php

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    public function save(Grade $grade, bool $flush = false): void
    {
        $this->getEntityManager()->persist($grade);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Grade $grade, bool $flush = false): void
    {
        $this->getEntityManager()->remove($grade);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.assignment', 'a')
            ->leftJoin('a.course', 'c')
            ->addSelect('a', 'c')
            ->andWhere('g.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->orderBy('g.gradedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCourse(int $courseId): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.assignment', 'a')
            ->leftJoin('g.student', 's')
            ->addSelect('a', 's')
            ->andWhere('a.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByAssignment(int $assignmentId): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.student', 's')
            ->addSelect('s')
            ->andWhere('g.assignment = :assignmentId')
            ->setParameter('assignmentId', $assignmentId)
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function calculateAverageForCourse(int $courseId): ?float
    {
        $result = $this->createQueryBuilder('g')
            ->select('AVG(g.percentage) as average')
            ->leftJoin('g.assignment', 'a')
            ->andWhere('a.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : null;
    }
}
