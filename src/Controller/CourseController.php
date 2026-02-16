<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\TeacherRepository;
use App\Repository\ClassroomRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/course')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository, SchoolRepository $schoolRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schoolId = $request->query->getInt('school');
        $courses = $courseRepository->findBySchools($schools, $schoolId ?: null);
        return $this->render('course/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/new', name: 'course_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        TeacherRepository $teacherRepository,
        ClassroomRepository $classroomRepository,
        SchoolRepository $schoolRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schoolId = $request->request->getInt('school') ?: $request->query->getInt('school');
        $teachers = $schoolId ? $teacherRepository->findBy(['school' => $schoolId], ['lastName' => 'ASC']) : [];
        $classrooms = $schoolId ? $classroomRepository->findBy(['school' => $schoolId], ['name' => 'ASC']) : [];

        if ($request->isMethod('POST')) {
            $school = $schoolId ? $schoolRepository->findOneByIdAndOwner($schoolId, $user) : null;
            if (!$school) {
                $this->addFlash('error', 'Veuillez sélectionner une école.');
                return $this->render('course/new.html.twig', [
                    'schools' => $schools,
                    'teachers' => $teachers,
                    'classrooms' => $classrooms,
                ]);
            }
            $course = new Course();
            $course->setSchool($school);
            $course->setCode($request->request->get('code'));
            $course->setName($request->request->get('name'));
            $course->setDescription($request->request->get('description'));
            $course->setCredits((int) $request->request->get('credits'));
            $course->setMaxStudents((int) $request->request->get('maxStudents'));
            $course->setSemester($request->request->get('semester'));

            $teacherId = $request->request->get('teacher');
            if ($teacherId) {
                $teacher = $teacherRepository->find($teacherId);
                if ($teacher && $teacher->getSchool() && $teacher->getSchool()->getId() === $school->getId()) {
                    $course->setTeacher($teacher);
                }
            }

            $classroomId = $request->request->get('classroom');
            if ($classroomId) {
                $classroom = $classroomRepository->find($classroomId);
                if ($classroom && $classroom->getSchool() && $classroom->getSchool()->getId() === $school->getId()) {
                    $course->setClassroom($classroom);
                }
            }

            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Cours créé avec succès !');
            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/new.html.twig', [
            'schools' => $schools,
            'teachers' => $teachers,
            'classrooms' => $classrooms,
        ]);
    }

    #[Route('/{id}', name: 'course_show', methods: ['GET'])]
    public function show(Course $course, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$course->getSchool() || $schoolRepository->findOneByIdAndOwner($course->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Cours non trouvé.');
        }
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'course_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Course $course,
        EntityManagerInterface $entityManager,
        TeacherRepository $teacherRepository,
        ClassroomRepository $classroomRepository,
        SchoolRepository $schoolRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$course->getSchool() || $schoolRepository->findOneByIdAndOwner($course->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Cours non trouvé.');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schoolId = $course->getSchool()?->getId();
        $teachers = $schoolId ? $teacherRepository->findBy(['school' => $schoolId], ['lastName' => 'ASC']) : [];
        $classrooms = $schoolId ? $classroomRepository->findBy(['school' => $schoolId], ['name' => 'ASC']) : [];

        if ($request->isMethod('POST')) {
            $newSchoolId = $request->request->getInt('school');
            $school = $newSchoolId ? $schoolRepository->findOneByIdAndOwner($newSchoolId, $user) : null;
            if ($school) {
                $course->setSchool($school);
            }
            $course->setCode($request->request->get('code'));
            $course->setName($request->request->get('name'));
            $course->setDescription($request->request->get('description'));
            $course->setCredits((int) $request->request->get('credits'));
            $course->setMaxStudents((int) $request->request->get('maxStudents'));
            $course->setSemester($request->request->get('semester'));

            $teacherId = $request->request->get('teacher');
            if ($teacherId && $school) {
                $teacher = $teacherRepository->find($teacherId);
                if ($teacher && $teacher->getSchool() && $teacher->getSchool()->getId() === $school->getId()) {
                    $course->setTeacher($teacher);
                }
            }

            $classroomId = $request->request->get('classroom');
            if ($classroomId && $school) {
                $classroom = $classroomRepository->find($classroomId);
                if ($classroom && $classroom->getSchool() && $classroom->getSchool()->getId() === $school->getId()) {
                    $course->setClassroom($classroom);
                } else {
                    $course->setClassroom(null);
                }
            } else {
                $course->setClassroom(null);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Cours mis à jour avec succès !');
            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'schools' => $schools,
            'teachers' => $teachers,
            'classrooms' => $classrooms,
        ]);
    }

    #[Route('/{id}', name: 'course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if ($user && $course->getSchool() && $schoolRepository->findOneByIdAndOwner($course->getSchool()->getId(), $user) !== null) {
            if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
                $entityManager->remove($course);
                $entityManager->flush();
                $this->addFlash('success', 'Cours supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('course_index');
    }
}
