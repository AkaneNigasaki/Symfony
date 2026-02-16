<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\SchoolRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

class MembershipController extends AbstractController
{
    #[Route('/join/{token}', name: 'membership_join', methods: ['GET', 'POST'])]
    public function join(Request $request, string $token, SchoolRepository $schoolRepository, StudentRepository $studentRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $school = $schoolRepository->findOneBy(['joinToken' => $token]);
        if (!$school) {
            $this->addFlash('error', 'Lien de partage invalide ou expiré.');
            return $this->redirectToRoute('home');
        }

        // Check if user already has a student profile in this school
        $existingStudent = $studentRepository->findOneBy(['user' => $user, 'school' => $school]);
        if ($existingStudent) {
            if ($existingStudent->getStatus() === 'accepted') {
                $this->addFlash('info', 'Vous êtes déjà membre de cette école.');
            } else {
                $this->addFlash('info', 'Votre demande est en attente d\'approbation.');
            }
            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            // Create a pending student profile for the user
            $student = new Student();
            $student->setUser($user);
            $student->setSchool($school);
            $student->setFirstName($user->getFirstName() ?? '');
            $student->setLastName($user->getName() ?? '');
            $student->setEmail($user->getEmail());
            $student->setStudentId('EXT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)));
            $student->setDateOfBirth(new \DateTimeImmutable('2000-01-01')); // Placeholder
            $student->setEnrollmentDate(new \DateTimeImmutable());
            $student->setStatus('pending');

            $entityManager->persist($student);
            $entityManager->flush();

            $logger->log(sprintf('Demande d\'adhésion envoyée pour l\'école: %s', $school->getName()), 'user-plus', $user);
            $this->addFlash('success', 'Votre demande d\'adhésion a été envoyée ! Un administrateur doit l\'approuver.');

            return $this->redirectToRoute('home');
        }

        return $this->render('membership/join_confirm.html.twig', [
            'school' => $school,
        ]);
    }

    #[Route('/school/{id}/requests', name: 'membership_requests', methods: ['GET'])]
    public function requests(int $id, SchoolRepository $schoolRepository, StudentRepository $studentRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $school = $schoolRepository->findOneByIdAndOwner($id, $user);
        if (!$school) {
            throw $this->createNotFoundException('School not found.');
        }

        $pendingRequests = $studentRepository->findBy(['school' => $school, 'status' => 'pending']);

        return $this->render('membership/requests.html.twig', [
            'school' => $school,
            'requests' => $pendingRequests,
        ]);
    }

    #[Route('/membership/{id}/approve', name: 'membership_approve', methods: ['POST'])]
    public function approve(int $id, StudentRepository $studentRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $admin = $this->getUser();
        if (!$admin) {
            return $this->redirectToRoute('app_login');
        }

        $student = $studentRepository->find($id);
        if (!$student) {
            throw $this->createNotFoundException('Request not found.');
        }

        // Verify admin owns the school
        if ($student->getSchool()->getOwner() !== $admin) {
            throw $this->createAccessDeniedException();
        }

        $student->setStatus('accepted');
        $entityManager->flush();

        $logger->log(sprintf('Demande acceptée pour: %s (%s)', $student->getFullName(), $student->getSchool()->getName()), 'check-circle');
        $this->addFlash('success', sprintf('La demande de %s a été acceptée.', $student->getFullName()));

        return $this->redirectToRoute('membership_requests', ['id' => $student->getSchool()->getId()]);
    }

    #[Route('/membership/{id}/reject', name: 'membership_reject', methods: ['POST'])]
    public function reject(int $id, StudentRepository $studentRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $admin = $this->getUser();
        if (!$admin) {
            return $this->redirectToRoute('app_login');
        }

        $student = $studentRepository->find($id);
        if (!$student) {
            throw $this->createNotFoundException('Request not found.');
        }

        // Verify admin owns the school
        if ($student->getSchool()->getOwner() !== $admin) {
            throw $this->createAccessDeniedException();
        }

        $student->setStatus('rejected');
        $entityManager->flush();

        $logger->log(sprintf('Demande rejetée pour: %s (%s)', $student->getFullName(), $student->getSchool()->getName()), 'x-circle');
        $this->addFlash('info', sprintf('La demande de %s a été rejetée.', $student->getFullName()));

        return $this->redirectToRoute('membership_requests', ['id' => $student->getSchool()->getId()]);
    }

    #[Route('/membership/{id}/edit', name: 'membership_edit_member', methods: ['GET', 'POST'])]
    public function editMember(Request $request, int $id, StudentRepository $studentRepository, EntityManagerInterface $entityManager, \App\Service\ActivityLogger $logger): Response
    {
        $admin = $this->getUser();
        if (!$admin) {
            return $this->redirectToRoute('app_login');
        }

        $student = $studentRepository->find($id);
        if (!$student) {
            throw $this->createNotFoundException('Member not found.');
        }

        // Verify admin owns the school
        if ($student->getSchool()->getOwner() !== $admin) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de cet établissement.');
        }

        if ($request->isMethod('POST')) {
            $student->setRole($request->request->get('role', 'student'));
            $student->setStatus($request->request->get('status', 'accepted'));

            $entityManager->flush();

            $logger->log(sprintf('Membre mis à jour: %s (%s)', $student->getFullName(), $student->getRole()), 'user-cog');
            $this->addFlash('success', sprintf('Le membre %s a été mis à jour.', $student->getFullName()));

            return $this->redirectToRoute('school_show', ['id' => $student->getSchool()->getId()]);
        }

        return $this->render('membership/edit_member.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/school/{id}/members', name: 'membership_school_members', methods: ['GET'])]
    public function listMembers(int $id, SchoolRepository $schoolRepository, StudentRepository $studentRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $school = $schoolRepository->findOneByIdAndOwner($id, $user);
        if (!$school) {
            throw $this->createNotFoundException('School not found.');
        }

        $members = $studentRepository->findBy(['school' => $school]);

        return $this->render('membership/members.html.twig', [
            'school' => $school,
            'members' => $members,
        ]);
    }
}
