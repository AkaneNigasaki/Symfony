<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/teacher')]
class TeacherController extends AbstractController
{
    #[Route('/', name: 'teacher_index', methods: ['GET'])]
    public function index(TeacherRepository $teacherRepository, SchoolRepository $schoolRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schoolId = $request->query->getInt('school');
        $teachers = $teacherRepository->findBySchools($schools, $schoolId ?: null);
        return $this->render('teacher/index.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    #[Route('/new', name: 'teacher_new', methods: ['GET', 'POST'])]
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
                return $this->render('teacher/new.html.twig', ['schools' => $schools]);
            }
            $teacher = new Teacher();
            $teacher->setSchool($school);
            $teacher->setFirstName($request->request->get('firstName'));
            $teacher->setLastName($request->request->get('lastName'));
            $teacher->setEmail($request->request->get('email'));
            $teacher->setPhoneNumber($request->request->get('phoneNumber'));
            $teacher->setSpecialization($request->request->get('specialization'));
            $teacher->setHireDate(new \DateTimeImmutable($request->request->get('hireDate')));

            $entityManager->persist($teacher);
            $entityManager->flush();

            $logger->log(sprintf('Nouvel enseignant ajouté: %s %s', $teacher->getFirstName(), $teacher->getLastName()), 'user-plus');

            $this->addFlash('success', 'Enseignant créé avec succès !');
            return $this->redirectToRoute('teacher_index');
        }

        return $this->render('teacher/new.html.twig', ['schools' => $schools]);
    }

    #[Route('/{id}', name: 'teacher_show', methods: ['GET'])]
    public function show(Teacher $teacher, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$teacher->getSchool() || $schoolRepository->findOneByIdAndOwner($teacher->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Teacher not found.');
        }
        return $this->render('teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}/edit', name: 'teacher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teacher $teacher, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$teacher->getSchool() || $schoolRepository->findOneByIdAndOwner($teacher->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Teacher not found.');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        if ($request->isMethod('POST')) {
            $schoolId = $request->request->getInt('school');
            $school = $schoolId ? $schoolRepository->findOneByIdAndOwner($schoolId, $user) : null;
            if ($school) {
                $teacher->setSchool($school);
            }
            $teacher->setFirstName($request->request->get('firstName'));
            $teacher->setLastName($request->request->get('lastName'));
            $teacher->setEmail($request->request->get('email'));
            $teacher->setPhoneNumber($request->request->get('phoneNumber'));
            $teacher->setSpecialization($request->request->get('specialization'));
            $teacher->setHireDate(new \DateTimeImmutable($request->request->get('hireDate')));

            $entityManager->flush();

            $logger->log(sprintf('Enseignant mis à jour: %s %s', $teacher->getFirstName(), $teacher->getLastName()), 'edit');

            $this->addFlash('success', 'Enseignant mis à jour avec succès !');
            return $this->redirectToRoute('teacher_index');
        }

        return $this->render('teacher/edit.html.twig', [
            'teacher' => $teacher,
            'schools' => $schools,
        ]);
    }

    #[Route('/{id}', name: 'teacher_delete', methods: ['POST'])]
    public function delete(Request $request, Teacher $teacher, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if ($user && $teacher->getSchool() && $schoolRepository->findOneByIdAndOwner($teacher->getSchool()->getId(), $user) !== null) {
            if ($this->isCsrfTokenValid('delete' . $teacher->getId(), $request->request->get('_token'))) {
                $teacherName = $teacher->getFullName();
                $entityManager->remove($teacher);
                $entityManager->flush();

                $logger->log(sprintf('Enseignant supprimé: %s', $teacherName), 'trash-2');

                $this->addFlash('success', 'Enseignant supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('teacher_index');
    }
}
