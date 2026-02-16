<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use App\Repository\CourseRepository;
use App\Repository\ClassroomRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/schedule')]
class ScheduleController extends AbstractController
{
    #[Route('/', name: 'schedule_index', methods: ['GET'])]
    public function index(ScheduleRepository $scheduleRepository, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schedules = $scheduleRepository->findBySchools($schools);
        return $this->render('schedule/index.html.twig', [
            'schedules' => $schedules,
        ]);
    }

    #[Route('/new', name: 'schedule_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CourseRepository $courseRepository,
        ClassroomRepository $classroomRepository,
        SchoolRepository $schoolRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $courses = $courseRepository->findBySchools($schools);
        $classrooms = $classroomRepository->findBySchools($schools);

        if ($request->isMethod('POST')) {
            $schedule = new Schedule();
            $courseId = $request->request->getInt('course');
            $course = null;
            if ($courseId) {
                $course = $courseRepository->find($courseId);
                if ($course && $course->getSchool() && $schoolRepository->findOneByIdAndOwner($course->getSchool()->getId(), $user)) {
                    $schedule->setCourse($course);
                }
            }
            $classroomId = $request->request->getInt('classroom');
            if ($classroomId) {
                $classroom = $classroomRepository->find($classroomId);
                if ($classroom && $classroom->getSchool() && $schoolRepository->findOneByIdAndOwner($classroom->getSchool()->getId(), $user)) {
                    $schedule->setClassroom($classroom);
                }
            }

            if (!$schedule->getCourse() || !$schedule->getClassroom()) {
                $this->addFlash('error', 'Veuillez sélectionner un cours et une salle valides.');
                return $this->render('schedule/new.html.twig', [
                    'courses' => $courses,
                    'classrooms' => $classrooms,
                ]);
            }

            $schedule->setDayOfWeek((int) $request->request->get('dayOfWeek'));
            $schedule->setStartTime(new \DateTimeImmutable($request->request->get('startTime')));
            $schedule->setEndTime(new \DateTimeImmutable($request->request->get('endTime')));
            $schedule->setEffectiveFrom(new \DateTimeImmutable($request->request->get('effectiveFrom')));
            $effectiveTo = $request->request->get('effectiveTo');
            if ($effectiveTo) {
                $schedule->setEffectiveTo(new \DateTimeImmutable($effectiveTo));
            }

            $entityManager->persist($schedule);
            $entityManager->flush();

            $this->addFlash('success', 'Emploi du temps créé avec succès !');
            return $this->redirectToRoute('schedule_index');
        }

        return $this->render('schedule/new.html.twig', [
            'courses' => $courses,
            'classrooms' => $classrooms,
        ]);
    }

    #[Route('/{id}', name: 'schedule_show', methods: ['GET'])]
    public function show(Schedule $schedule, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$schedule->getCourse() || !$schedule->getCourse()->getSchool() || $schoolRepository->findOneByIdAndOwner($schedule->getCourse()->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Emploi du temps non trouvé.');
        }
        return $this->render('schedule/show.html.twig', [
            'schedule' => $schedule,
        ]);
    }

    #[Route('/{id}', name: 'schedule_delete', methods: ['POST'])]
    public function delete(Request $request, Schedule $schedule, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if ($user && $schedule->getCourse() && $schedule->getCourse()->getSchool() && $schoolRepository->findOneByIdAndOwner($schedule->getCourse()->getSchool()->getId(), $user) !== null) {
            if ($this->isCsrfTokenValid('delete' . $schedule->getId(), $request->request->get('_token'))) {
                $entityManager->remove($schedule);
                $entityManager->flush();
                $this->addFlash('success', 'Emploi du temps supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('schedule_index');
    }
}
