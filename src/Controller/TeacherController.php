<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/teacher')]
class TeacherController extends AbstractController
{
    #[Route('/', name: 'teacher_index', methods: ['GET'])]
    public function index(TeacherRepository $teacherRepository): Response
    {
        $teachers = $teacherRepository->findAllOrderedByName();
        return $this->render('teacher/index.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    #[Route('/new', name: 'teacher_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $teacher = new Teacher();
            $teacher->setFirstName($request->request->get('firstName'));
            $teacher->setLastName($request->request->get('lastName'));
            $teacher->setEmail($request->request->get('email'));
            $teacher->setPhoneNumber($request->request->get('phoneNumber'));
            $teacher->setSpecialization($request->request->get('specialization'));
            $teacher->setHireDate(new \DateTimeImmutable($request->request->get('hireDate')));

            $entityManager->persist($teacher);
            $entityManager->flush();

            $this->addFlash('success', 'Enseignant créé avec succès !');
            return $this->redirectToRoute('teacher_index');
        }

        return $this->render('teacher/new.html.twig');
    }

    #[Route('/{id}', name: 'teacher_show', methods: ['GET'])]
    public function show(Teacher $teacher): Response
    {
        return $this->render('teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}/edit', name: 'teacher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $teacher->setFirstName($request->request->get('firstName'));
            $teacher->setLastName($request->request->get('lastName'));
            $teacher->setEmail($request->request->get('email'));
            $teacher->setPhoneNumber($request->request->get('phoneNumber'));
            $teacher->setSpecialization($request->request->get('specialization'));
            $teacher->setHireDate(new \DateTimeImmutable($request->request->get('hireDate')));

            $entityManager->flush();

            $this->addFlash('success', 'Enseignant mis à jour avec succès !');
            return $this->redirectToRoute('teacher_index');
        }

        return $this->render('teacher/edit.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}', name: 'teacher_delete', methods: ['POST'])]
    public function delete(Request $request, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $teacher->getId(), $request->request->get('_token'))) {
            $entityManager->remove($teacher);
            $entityManager->flush();

            $this->addFlash('success', 'Enseignant supprimé avec succès !');
        }

        return $this->redirectToRoute('teacher_index');
    }
}
