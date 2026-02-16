<?php

namespace App\Controller;

use App\Entity\School;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/school')]
class SchoolController extends AbstractController
{
    #[Route('/', name: 'school_index', methods: ['GET'])]
    public function index(SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $schools = $schoolRepository->findByOwnerOrderedByName($user);
        return $this->render('school/index.html.twig', [
            'schools' => $schools,
        ]);
    }

    #[Route('/new', name: 'school_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($request->isMethod('POST')) {
            $school = new School();
            $school->setOwner($user);
            $school->setName($request->request->get('name'));
            $school->setAddress($request->request->get('address'));
            $school->setPhone($request->request->get('phone'));
            $school->setEmail($request->request->get('email'));
            $school->setDescription($request->request->get('description'));

            $entityManager->persist($school);
            $entityManager->flush();

            $logger->log(sprintf('Nouvelle école créée: %s', $school->getName()), 'building');

            $this->addFlash('success', 'École créée avec succès !');
            return $this->redirectToRoute('school_index');
        }

        return $this->render('school/new.html.twig');
    }

    #[Route('/{id}', name: 'school_show', methods: ['GET'])]
    public function show(int $id, SchoolRepository $schoolRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }
        $school = $schoolRepository->findOneByIdAndUser($id, $user);
        if (!$school) {
            throw $this->createNotFoundException('School not found or access denied.');
        }
        $school = $schoolRepository->findWithStats($school->getId()) ?? $school;
        return $this->render('school/show.html.twig', [
            'school' => $school,
        ]);
    }

    #[Route('/{id}/edit', name: 'school_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, SchoolRepository $schoolRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }
        $school = $schoolRepository->find($id);
        if (!$school) {
            throw $this->createNotFoundException('School not found.');
        }

        // Explicitly check ownership
        if ($school->getOwner() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cette école.');
        }

        if ($request->isMethod('POST')) {
            $school->setName($request->request->get('name'));
            $school->setAddress($request->request->get('address'));
            $school->setPhone($request->request->get('phone'));
            $school->setEmail($request->request->get('email'));
            $school->setDescription($request->request->get('description'));

            $entityManager->flush();

            $logger->log(sprintf('École mise à jour: %s', $school->getName()), 'edit');

            $this->addFlash('success', 'École mise à jour avec succès !');
            return $this->redirectToRoute('school_index');
        }

        return $this->render('school/edit.html.twig', [
            'school' => $school,
        ]);
    }

    #[Route('/{id}', name: 'school_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, SchoolRepository $schoolRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }
        $school = $schoolRepository->find($id);
        if (!$school) {
            throw $this->createNotFoundException('School not found.');
        }

        // Explicitly check ownership
        if ($school->getOwner() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cette école.');
        }

        if ($this->isCsrfTokenValid('delete' . $school->getId(), $request->request->get('_token'))) {
            $schoolName = $school->getName();
            $entityManager->remove($school);
            $entityManager->flush();

            $logger->log(sprintf('École supprimée: %s', $schoolName), 'trash-2');

            $this->addFlash('success', 'École supprimée avec succès !');
        }

        return $this->redirectToRoute('school_index');
    }

    #[Route('/{id}/generate-token', name: 'school_generate_token', methods: ['POST'])]
    public function generateToken(int $id, SchoolRepository $schoolRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $school = $schoolRepository->findOneByIdAndOwner($id, $user);
        if (!$school) {
            throw $this->createNotFoundException('School not found.');
        }

        $token = bin2hex(random_bytes(32));
        $school->setJoinToken($token);
        $entityManager->flush();

        $logger->log(sprintf('Lien de partage généré pour: %s', $school->getName()), 'link');
        $this->addFlash('success', 'Lien de partage généré avec succès !');

        return $this->redirectToRoute('school_show', ['id' => $school->getId()]);
    }

    #[Route('/{id}/reset-token', name: 'school_reset_token', methods: ['POST'])]
    public function resetToken(int $id, SchoolRepository $schoolRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $school = $schoolRepository->findOneByIdAndOwner($id, $user);
        if (!$school) {
            throw $this->createNotFoundException('School not found.');
        }

        $school->setJoinToken(null);
        $entityManager->flush();

        $logger->log(sprintf('Lien de partage désactivé pour: %s', $school->getName()), 'link-2');
        $this->addFlash('info', 'Lien de partage désactivé.');

        return $this->redirectToRoute('school_show', ['id' => $school->getId()]);
    }
}
