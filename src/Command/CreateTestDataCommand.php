<?php

namespace App\Command;

use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-test-data',
    description: 'Créer des données de test pour EduManage',
)]
class CreateTestDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $user->setEmail('admin@edumanage.fr');
        $user->setFirstName('Admin');
        $user->setLastName('EduManage');
        $user->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);

        $teachers = [
            ['firstName' => 'Marie', 'lastName' => 'Dupont', 'email' => 'marie.dupont@school.fr', 'phone' => '0612345678', 'specialization' => 'Mathématiques', 'hireDate' => '2020-09-01'],
            ['firstName' => 'Jean', 'lastName' => 'Martin', 'email' => 'jean.martin@school.fr', 'phone' => '0623456789', 'specialization' => 'Physique-Chimie', 'hireDate' => '2019-09-01'],
            ['firstName' => 'Sophie', 'lastName' => 'Bernard', 'email' => 'sophie.bernard@school.fr', 'phone' => '0634567890', 'specialization' => 'Français', 'hireDate' => '2021-09-01'],
            ['firstName' => 'Pierre', 'lastName' => 'Dubois', 'email' => 'pierre.dubois@school.fr', 'phone' => '0645678901', 'specialization' => 'Histoire-Géographie', 'hireDate' => '2018-09-01'],
            ['firstName' => 'Isabelle', 'lastName' => 'Leroy', 'email' => 'isabelle.leroy@school.fr', 'phone' => '0656789012', 'specialization' => 'Anglais', 'hireDate' => '2022-09-01'],
        ];

        $teacherObjects = [];
        foreach ($teachers as $data) {
            $teacher = new Teacher();
            $teacher->setFirstName($data['firstName']);
            $teacher->setLastName($data['lastName']);
            $teacher->setEmail($data['email']);
            $teacher->setPhoneNumber($data['phone']);
            $teacher->setSpecialization($data['specialization']);
            $teacher->setHireDate(new \DateTimeImmutable($data['hireDate']));
            
            $this->entityManager->persist($teacher);
            $teacherObjects[] = $teacher;
        }

        $subjects = [
            ['code' => 'MATH101', 'name' => 'Mathématiques Niveau 1', 'description' => 'Algèbre et géométrie de base', 'coefficient' => 4, 'hoursPerWeek' => 6, 'teacher' => 0],
            ['code' => 'PHYS201', 'name' => 'Physique Générale', 'description' => 'Mécanique et thermodynamique', 'coefficient' => 3, 'hoursPerWeek' => 4, 'teacher' => 1],
            ['code' => 'FRAN101', 'name' => 'Français', 'description' => 'Littérature et grammaire', 'coefficient' => 3, 'hoursPerWeek' => 5, 'teacher' => 2],
            ['code' => 'HIST101', 'name' => 'Histoire', 'description' => 'Histoire moderne et contemporaine', 'coefficient' => 2, 'hoursPerWeek' => 3, 'teacher' => 3],
            ['code' => 'ANGL101', 'name' => 'Anglais LV1', 'description' => 'Anglais niveau intermédiaire', 'coefficient' => 3, 'hoursPerWeek' => 4, 'teacher' => 4],
            ['code' => 'MATH201', 'name' => 'Mathématiques Niveau 2', 'description' => 'Analyse et probabilités', 'coefficient' => 4, 'hoursPerWeek' => 6, 'teacher' => 0],
            ['code' => 'CHEM101', 'name' => 'Chimie', 'description' => 'Chimie organique et inorganique', 'coefficient' => 2, 'hoursPerWeek' => 3, 'teacher' => 1],
            ['code' => 'GEOG101', 'name' => 'Géographie', 'description' => 'Géographie physique et humaine', 'coefficient' => 2, 'hoursPerWeek' => 2, 'teacher' => 3],
        ];

        foreach ($subjects as $data) {
            $subject = new Subject();
            $subject->setCode($data['code']);
            $subject->setName($data['name']);
            $subject->setDescription($data['description']);
            $subject->setCoefficient($data['coefficient']);
            $subject->setHoursPerWeek($data['hoursPerWeek']);
            if (isset($teacherObjects[$data['teacher']])) {
                $subject->setTeacher($teacherObjects[$data['teacher']]);
            }
            
            $this->entityManager->persist($subject);
        }

        $this->entityManager->flush();

        $io->success('Données de test créées avec succès!');
        $io->section('Utilisateur créé:');
        $io->listing([
            'Email: admin@edumanage.fr',
            'Mot de passe: admin123',
        ]);
        $io->text('5 enseignants créés');
        $io->text('8 matières créées');

        return Command::SUCCESS;
    }
}
