<?php

namespace App\Controller;

use App\Repository\ClassroomRepository;
use App\Repository\CourseRepository;
use App\Repository\SchoolRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        SchoolRepository $schoolRepository,
        ClassroomRepository $classroomRepo,
        TeacherRepository $teacherRepo,
        CourseRepository $courseRepo,
        StudentRepository $studentRepo,
        ScheduleRepository $scheduleRepo,
        \App\Repository\UserActivityRepository $activityRepo
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $activities = $activityRepo->findRecentByUser($user, 10);

        // Fetch schools where the user is an accepted student
        $memberships = $studentRepo->findBy(['user' => $user, 'status' => 'accepted']);
        $studentSchools = array_map(fn($m) => $m->getSchool(), $memberships);

        $stats = [
            'schools' => count($schools),
            'classrooms' => count($classroomRepo->findBySchools($schools)),
            'teachers' => count($teacherRepo->findBySchools($schools)),
            'courses' => count($courseRepo->findBySchools($schools)),
            'students' => count($studentRepo->findBySchools($schools)),
            'schedules' => count($scheduleRepo->findBySchools($schools)),
        ];

        return $this->render('home/index.html.twig', [
            'stats' => $stats,
            'activities' => $activities,
            'studentSchools' => $studentSchools,
        ]);
    }
}
