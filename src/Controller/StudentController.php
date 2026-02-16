<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository, SchoolRepository $schoolRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schoolId = $request->query->getInt('school');
        $students = $studentRepository->findBySchools($schools, $schoolId ?: null);
        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/new', name: 'student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        if ($request->isMethod('POST')) {
            $schoolId = $request->request->getInt('school');
            $school = $schoolId ? $schoolRepository->findOneByIdAndOwner($schoolId, $user) : null;
            if (!$school) {
                $this->addFlash('error', 'Veuillez sélectionner une école.');
                return $this->render('student/new.html.twig', ['schools' => $schools]);
            }
            $student = new Student();
            $student->setSchool($school);
            $student->setStudentId($request->request->get('studentId'));
            $student->setFirstName($request->request->get('firstName'));
            $student->setLastName($request->request->get('lastName'));
            $student->setEmail($request->request->get('email'));
            $student->setDateOfBirth(new \DateTimeImmutable($request->request->get('dateOfBirth')));
            $student->setEnrollmentDate(new \DateTimeImmutable($request->request->get('enrollmentDate')));

            $entityManager->persist($student);
            $entityManager->flush();

            $logger->log(sprintf('Nouvel étudiant inscrit: %s %s', $student->getFirstName(), $student->getLastName()), 'users');

            $this->addFlash('success', 'Étudiant créé avec succès !');
            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/new.html.twig', ['schools' => $schools]);
    }

    #[Route('/{id}', name: 'student_show', methods: ['GET'])]
    public function show(Student $student, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$student->getSchool() || $schoolRepository->findOneByIdAndOwner($student->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Student not found.');
        }
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$student->getSchool() || $schoolRepository->findOneByIdAndOwner($student->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Student not found.');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        if ($request->isMethod('POST')) {
            $schoolId = $request->request->getInt('school');
            $school = $schoolId ? $schoolRepository->findOneByIdAndOwner($schoolId, $user) : null;
            if ($school) {
                $student->setSchool($school);
            }
            $student->setStudentId($request->request->get('studentId'));
            $student->setFirstName($request->request->get('firstName'));
            $student->setLastName($request->request->get('lastName'));
            $student->setEmail($request->request->get('email'));
            $student->setDateOfBirth(new \DateTimeImmutable($request->request->get('dateOfBirth')));
            $student->setEnrollmentDate(new \DateTimeImmutable($request->request->get('enrollmentDate')));

            $entityManager->flush();

            $logger->log(sprintf('Étudiant mis à jour: %s %s', $student->getFirstName(), $student->getLastName()), 'edit');

            $this->addFlash('success', 'Étudiant mis à jour avec succès !');
            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'schools' => $schools,
        ]);
    }

    #[Route('/{id}', name: 'student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if ($user && $student->getSchool() && $schoolRepository->findOneByIdAndOwner($student->getSchool()->getId(), $user) !== null) {
            if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
                $studentName = $student->getFullName();
                $entityManager->remove($student);
                $entityManager->flush();

                $logger->log(sprintf('Étudiant supprimé: %s', $studentName), 'trash-2');

                $this->addFlash('success', 'Étudiant supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('student_index');
    }
}
