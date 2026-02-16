<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @param list<School> $schools
     * @return list<Course>
     */
    public function findBySchools(array $schools, ?int $schoolId = null): array
    {
        if (empty($schools)) {
            return [];
        }
        $qb = $this->createQueryBuilder('c')
            ->where('c.school IN (:schools)')
            ->setParameter('schools', $schools)
            ->orderBy('c.code', 'ASC');
        if ($schoolId !== null) {
            $qb->andWhere('c.school = :schoolId')->setParameter('schoolId', $schoolId);
        }
        return $qb->getQuery()->getResult();
    }

    public function save(Course $course, bool $flush = false): void
    {
        $this->getEntityManager()->persist($course);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Course $course, bool $flush = false): void
    {
        $this->getEntityManager()->remove($course);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCode(string $code): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBySemester(string $semester): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.semester = :semester')
            ->setParameter('semester', $semester)
            ->orderBy('c.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('c.semester', 'DESC')
            ->addOrderBy('c.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithAvailableSeats(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.enrollments', 'e')
            ->andWhere('e.status = :status OR e.id IS NULL')
            ->setParameter('status', 'enrolled')
            ->groupBy('c.id')
            ->having('COUNT(e.id) < c.maxStudents')
            ->getQuery()
            ->getResult();
    }
}
