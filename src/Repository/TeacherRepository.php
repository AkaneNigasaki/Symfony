<?php

namespace App\Repository;

use App\Entity\School;
use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    /**
     * @param list<School> $schools
     * @return list<Teacher>
     */
    public function findBySchools(array $schools, ?int $schoolId = null): array
    {
        if (empty($schools)) {
            return [];
        }
        $qb = $this->createQueryBuilder('t')
            ->where('t.school IN (:schools)')
            ->setParameter('schools', $schools)
            ->orderBy('t.lastName', 'ASC')
            ->addOrderBy('t.firstName', 'ASC');
        if ($schoolId !== null) {
            $qb->andWhere('t.school = :schoolId')->setParameter('schoolId', $schoolId);
        }
        return $qb->getQuery()->getResult();
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

    public function findWithCourses(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.courses', 'c')
            ->addSelect('c')
            ->orderBy('t.lastName', 'ASC')
            ->addOrderBy('t.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
