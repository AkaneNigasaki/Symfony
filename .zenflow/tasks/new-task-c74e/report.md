# Résumé du projet

## Type de projet
Application web de gestion scolaire

## Technologies principales
- **Backend**: Symfony 7/8 (PHP)
- **Frontend**: Twig + Tailwind CSS
- **Base de données**: Doctrine ORM + SQLite/MySQL
- **Assets**: Webpack Encore
- **Features IA**: Symfony AI Bundle

## Fonctionnalités principales

### Gestion administrative
- Écoles multiples par propriétaire
- Classes/Salles de cours
- Cours avec programmes
- Emplois du temps

### Gestion des utilisateurs
- Étudiants (avec système d'inscription et d'acceptation)
- Enseignants
- Propriétaires d'écoles
- Authentification et sécurité

### Gestion académique
- Devoirs (assignments)
- Notes (grades)
- Inscriptions aux cours (enrollments)
- Suivi de l'activité utilisateur

## Structure du code
- **Entités**: Student, Teacher, School, Course, Classroom, Schedule, Assignment, Grade, Enrollment, User, UserActivity
- **Contrôleurs**: 11 contrôleurs pour gérer les différentes fonctionnalités
- **Templates**: Structure Twig organisée par fonctionnalité
- **Migrations**: Doctrine migrations pour la gestion du schéma de base de données
