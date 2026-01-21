# Forum Symfony

Projet de forum réalisé avec **Symfony** (PHP) et une base de données relationnelle.

## Prérequis
- PHP 8.x
- Composer
- (Optionnel) Docker + Docker Compose

## Installation
```bash
composer install
```

Créer et configurer la base de données dans `.env` puis appliquer les migrations :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Lancer le projet
```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

## Tests
```bash
php bin/phpunit
```

## Fonctionnalités (résumé)
- Gestion des utilisateurs (inscription/connexion)
- Thèmes et sujets de discussion
- Messages dans les sujets
- Modération

## Auteur
Nom : Simaoui
Prénom : Imed
Groupe : (solo)

## Pour le professeur
Ce dépôt contient le code source complet ainsi que les migrations et la configuration Symfony.
