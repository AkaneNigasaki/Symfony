<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/classroom')]
class ClassroomController extends AbstractController
{
    #[Route('/', name: 'classroom_index', methods: ['GET'])]
    public function index(ClassroomRepository $classroomRepository, SchoolRepository $schoolRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        $schoolId = $request->query->getInt('school');
        $classrooms = $classroomRepository->findBySchools($schools, $schoolId ?: null);
        return $this->render('classroom/index.html.twig', [
            'classrooms' => $classrooms,
        ]);
    }

    #[Route('/new', name: 'classroom_new', methods: ['GET', 'POST'])]
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
                return $this->render('classroom/new.html.twig', ['schools' => $schools]);
            }
            $classroom = new Classroom();
            $classroom->setSchool($school);
            $classroom->setName($request->request->get('name'));
            $classroom->setCapacity((int) $request->request->get('capacity'));
            $classroom->setType($request->request->get('type'));
            $classroom->setLocation($request->request->get('location'));

            $facilities = $request->request->get('facilities');
            if ($facilities) {
                $classroom->setFacilities(array_map('trim', explode(',', $facilities)));
            }

            $entityManager->persist($classroom);
            $entityManager->flush();

            $logger->log(sprintf('Nouvelle classe créée: %s (%s)', $classroom->getName(), $school->getName()), 'door-open');

            $this->addFlash('success', 'Classe créée avec succès !');
            return $this->redirectToRoute('classroom_index');
        }

        return $this->render('classroom/new.html.twig', ['schools' => $schools]);
    }

    #[Route('/{id}', name: 'classroom_show', methods: ['GET'])]
    public function show(Classroom $classroom, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$classroom->getSchool() || $schoolRepository->findOneByIdAndOwner($classroom->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Classroom not found.');
        }
        return $this->render('classroom/show.html.twig', [
            'classroom' => $classroom,
        ]);
    }

    #[Route('/{id}/edit', name: 'classroom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classroom $classroom, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$classroom->getSchool() || $schoolRepository->findOneByIdAndOwner($classroom->getSchool()->getId(), $user) === null) {
            throw $this->createNotFoundException('Classroom not found.');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        if ($request->isMethod('POST')) {
            $schoolId = $request->request->getInt('school');
            $school = $schoolId ? $schoolRepository->findOneByIdAndOwner($schoolId, $user) : null;
            if ($school) {
                $classroom->setSchool($school);
            }
            $classroom->setName($request->request->get('name'));
            $classroom->setCapacity((int) $request->request->get('capacity'));
            $classroom->setType($request->request->get('type'));
            $classroom->setLocation($request->request->get('location'));

            $facilities = $request->request->get('facilities');
            if ($facilities) {
                $classroom->setFacilities(array_map('trim', explode(',', $facilities)));
            }

            $entityManager->flush();

            $logger->log(sprintf('Classe mise à jour: %s', $classroom->getName()), 'edit');

            $this->addFlash('success', 'Classe mise à jour avec succès !');
            return $this->redirectToRoute('classroom_index');
        }

        return $this->render('classroom/edit.html.twig', [
            'classroom' => $classroom,
            'schools' => $schools,
        ]);
    }

    #[Route('/{id}', name: 'classroom_delete', methods: ['POST'])]
    public function delete(Request $request, Classroom $classroom, EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if ($user && $classroom->getSchool() && $schoolRepository->findOneByIdAndOwner($classroom->getSchool()->getId(), $user) !== null) {
            if ($this->isCsrfTokenValid('delete' . $classroom->getId(), $request->request->get('_token'))) {
                $classroomName = $classroom->getName();
                $entityManager->remove($classroom);
                $entityManager->flush();

                $logger->log(sprintf('Classe supprimée: %s', $classroomName), 'trash-2');

                $this->addFlash('success', 'Classe supprimée avec succès !');
            }
        }

        return $this->redirectToRoute('classroom_index');
    }
}
