# Projet Symfony

Ce projet est construit avec **Symfony** pour un gestion d'école et matère, utilise **Composer** pour les dépendances PHP et Tailwind pour la gestion des assets front-end.

---

## Prérequis

Assurez-vous d’avoir installé les outils suivants :

| Outil | Version minimale | Vérification |
|-------|------------------|--------------|
| PHP | >= 8.1 | `php -v` |
| Composer | >= 2.9.5 | `composer --version` |
| Node.js | >= 16 | `node -v` |
| npm | >= 11.4.2 | `npm -v` |
| Git | Dernière version | `git --version` |
| Symfony CLI | 2.9.5 | `symfony -v` |

##  Installation des dépendances
```bash
composer install
npm install
```
## Configuration importante : Fichier .env

### Assurez-vous de modifier le fichier .env pour la gestion de la base de données

Le fichier `.env` contient les variables d'environnement de votre application, assurer vous de le modifier pour quelle type de base de données utilisé

## Gestion des migrations de base de données

### Créer une migration

```bash
# Générer une migration à partir des changements des entités
php bin/console make:migration
```
