<?php

namespace App\Repository;

use App\Entity\School;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * @param list<School> $schools
     * @return list<Student>
     */
    public function findBySchools(array $schools, ?int $schoolId = null): array
    {
        if (empty($schools)) {
            return [];
        }
        $qb = $this->createQueryBuilder('s')
            ->where('s.school IN (:schools)')
            ->setParameter('schools', $schools)
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC');
        if ($schoolId !== null) {
            $qb->andWhere('s.school = :schoolId')->setParameter('schoolId', $schoolId);
        }
        return $qb->getQuery()->getResult();
    }

    public function save(Student $student, bool $flush = false): void
    {
        $this->getEntityManager()->persist($student);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Student $student, bool $flush = false): void
    {
        $this->getEntityManager()->remove($student);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByStudentId(string $studentId): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.studentId = :studentId')
            ->setParameter('studentId', $studentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEmail(string $email): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findWithEnrollments(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.enrollments', 'e')
            ->leftJoin('e.course', 'c')
            ->addSelect('e', 'c')
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $query): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.firstName LIKE :query OR s.lastName LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
