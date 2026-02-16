<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error = null;
        $email = '';
        $name = '';
        if ($request->isMethod('POST')) {
            $email = trim((string) $request->request->get('email', ''));
            $name = trim((string) $request->request->get('name', ''));
            $firstName = trim((string) $request->request->get('first_name', ''));
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');

            if (!$email || !$name || !$password) {
                $error = 'Veuillez remplir tous les champs obligatoires.';
            } elseif ($userRepository->findOneBy(['email' => $email])) {
                $error = 'Cet email est déjà enregistré.';
            } elseif (\strlen($password) < 6) {
                $error = 'Le mot de passe doit faire au moins 6 caractères.';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $user = new User();
                $user->setEmail($email);
                $user->setName($name);
                $user->setFirstName($firstName);
                $user->setPassword($passwordHasher->hashPassword($user, $password));

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Compte créé ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'error' => $error,
            'last_email' => $email,
            'last_name' => $name,
        ]);
    }
}
