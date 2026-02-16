<?php

namespace App\Repository;

use App\Entity\Enrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    public function save(Enrollment $enrollment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($enrollment);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Enrollment $enrollment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($enrollment);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.course', 'c')
            ->leftJoin('c.teacher', 't')
            ->addSelect('c', 't')
            ->andWhere('e.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->orderBy('e.enrollmentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCourse(int $courseId): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.student', 's')
            ->addSelect('s')
            ->andWhere('e.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveEnrollment(int $studentId, int $courseId): ?Enrollment
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.student = :studentId')
            ->andWhere('e.course = :courseId')
            ->andWhere('e.status = :status')
            ->setParameter('studentId', $studentId)
            ->setParameter('courseId', $courseId)
            ->setParameter('status', 'enrolled')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countActiveEnrollmentsByCourse(int $courseId): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.course = :courseId')
            ->andWhere('e.status = :status')
            ->setParameter('courseId', $courseId)
            ->setParameter('status', 'enrolled')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
