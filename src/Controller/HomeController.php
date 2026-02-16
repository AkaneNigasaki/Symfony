<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        TeacherRepository $teacherRepo,
        SubjectRepository $subjectRepo
    ): Response {
        $teachers = $teacherRepo->findAllOrderedByName();
        $subjects = $subjectRepo->findAllOrderedByCode();
        
        $stats = [
            'teachers' => count($teachers),
            'subjects' => count($subjects),
            'assigned_subjects' => count($subjectRepo->createQueryBuilder('s')
                ->where('s.teacher IS NOT NULL')
                ->getQuery()
                ->getResult()),
            'unassigned_subjects' => count($subjectRepo->findWithoutTeacher()),
        ];

        return $this->render('home/index.html.twig', [
            'stats' => $stats,
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }
}
