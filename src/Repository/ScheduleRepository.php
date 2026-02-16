<?php

namespace App\Repository;

use App\Entity\Schedule;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * @param list<School> $schools
     * @return list<Schedule>
     */
    public function findBySchools(array $schools): array
    {
        if (empty($schools)) {
            return [];
        }
        return $this->createQueryBuilder('s')
            ->innerJoin('s.course', 'c')
            ->where('c.school IN (:schools)')
            ->setParameter('schools', $schools)
            ->orderBy('s.dayOfWeek', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Schedule $schedule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($schedule);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Schedule $schedule, bool $flush = false): void
    {
        $this->getEntityManager()->remove($schedule);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCourse(int $courseId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('s.dayOfWeek', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByClassroom(int $classroomId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.classroom = :classroomId')
            ->setParameter('classroomId', $classroomId)
            ->orderBy('s.dayOfWeek', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findConflictingSchedules(
        int $classroomId,
        int $dayOfWeek,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime,
        ?int $excludeScheduleId = null
    ): array {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.classroom = :classroomId')
            ->andWhere('s.dayOfWeek = :dayOfWeek')
            ->andWhere('(s.startTime < :endTime AND s.endTime > :startTime)')
            ->setParameter('classroomId', $classroomId)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime);

        if ($excludeScheduleId !== null) {
            $qb->andWhere('s.id != :excludeId')
               ->setParameter('excludeId', $excludeScheduleId);
        }

        return $qb->getQuery()->getResult();
    }
}
