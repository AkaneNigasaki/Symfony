<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    #[Route('/', name: 'subject_index', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository): Response
    {
        $subjects = $subjectRepository->findAllOrderedByCode();
        return $this->render('subject/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }

    #[Route('/new', name: 'subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TeacherRepository $teacherRepository): Response
    {
        $teachers = $teacherRepository->findAllOrderedByName();
        
        if ($request->isMethod('POST')) {
            $subject = new Subject();
            $subject->setCode($request->request->get('code'));
            $subject->setName($request->request->get('name'));
            $subject->setDescription($request->request->get('description'));
            $subject->setCoefficient((int) $request->request->get('coefficient'));
            $subject->setHoursPerWeek((int) $request->request->get('hoursPerWeek'));
            
            $teacherId = $request->request->getInt('teacher');
            if ($teacherId) {
                $teacher = $teacherRepository->find($teacherId);
                if ($teacher) {
                    $subject->setTeacher($teacher);
                }
            }

            $entityManager->persist($subject);
            $entityManager->flush();

            $this->addFlash('success', 'Matière créée avec succès !');
            return $this->redirectToRoute('subject_index');
        }

        return $this->render('subject/new.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    #[Route('/{id}', name: 'subject_show', methods: ['GET'])]
    public function show(Subject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    #[Route('/{id}/edit', name: 'subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subject $subject, EntityManagerInterface $entityManager, TeacherRepository $teacherRepository): Response
    {
        $teachers = $teacherRepository->findAllOrderedByName();
        
        if ($request->isMethod('POST')) {
            $subject->setCode($request->request->get('code'));
            $subject->setName($request->request->get('name'));
            $subject->setDescription($request->request->get('description'));
            $subject->setCoefficient((int) $request->request->get('coefficient'));
            $subject->setHoursPerWeek((int) $request->request->get('hoursPerWeek'));
            
            $teacherId = $request->request->getInt('teacher');
            if ($teacherId) {
                $teacher = $teacherRepository->find($teacherId);
                if ($teacher) {
                    $subject->setTeacher($teacher);
                }
            } else {
                $subject->setTeacher(null);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Matière mise à jour avec succès !');
            return $this->redirectToRoute('subject_index');
        }

        return $this->render('subject/edit.html.twig', [
            'subject' => $subject,
            'teachers' => $teachers,
        ]);
    }

    #[Route('/{id}', name: 'subject_delete', methods: ['POST'])]
    public function delete(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $subject->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subject);
            $entityManager->flush();

            $this->addFlash('success', 'Matière supprimée avec succès !');
        }

        return $this->redirectToRoute('subject_index');
    }
}
