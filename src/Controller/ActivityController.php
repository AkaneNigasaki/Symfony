<?php

namespace App\Controller;

use App\Repository\UserActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activites')]
class ActivityController extends AbstractController
{
    #[Route('/', name: 'activity_index')]
    public function index(UserActivityRepository $activityRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }

        // Get all recent activities for the user
        $activities = $activityRepository->findRecentByUser($user, 50);

        return $this->render('activity/index.html.twig', [
            'activities' => $activities,
        ]);
    }
}
