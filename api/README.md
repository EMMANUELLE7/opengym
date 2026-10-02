# OpenGym — API Symfony

API REST développée avec Symfony 7.4 pour gérer les inscriptions des membres à des séances de sport.

## Fonctionnalités

L'API permet :

* l'authentification des utilisateurs avec JWT ;
* la consultation des séances à venir ;
* la consultation du détail d'une séance ;
* la réservation d'une séance par un membre ;
* l'annulation d'une réservation ;
* la consultation des réservations du membre connecté ;
* la création, modification et suppression d'une séance par un coach ;
* la consultation des participants d'une séance par un coach.

## Technologies

* PHP 8.2
* Symfony 7.4 LTS
* Doctrine ORM
* MariaDB
* LexikJWTAuthenticationBundle
* NelmioCorsBundle

## Installation

Cloner le projet puis entrer dans le dossier de l'API :

```bash
cd api
composer install
```

Configurer la base de données dans `.env.local` :

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/opengym?serverVersion=mariadb-10.4.32&charset=utf8mb4"
```

Créer la base de données et appliquer les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Charger les données de test :

```bash
php bin/console doctrine:fixtures:load
```

Lancer le serveur Symfony :

```bash
symfony server:start
```

L'API est accessible à :

```text
http://127.0.0.1:8000
```

## JWT

Les clés JWT sont stockées dans :

```text
api/config/jwt/
```

Elles peuvent être générées avec :

```bash
php bin/console lexik:jwt:generate-keypair
```

Les informations sensibles, le fichier `.env` et les clés JWT ne doivent pas être envoyés sur GitHub.

## Comptes de test

### Coach

```text
Email : coach@opengym.test
Mot de passe : Coach123!
Rôle : ROLE_COACH
```

### Membres

```text
Email : membre1@opengym.test
Mot de passe : Membre123!
Rôle : ROLE_MEMBER
```

```text
Email : membre2@opengym.test
Mot de passe : Membre123!
Rôle : ROLE_MEMBER
```

```text
Email : membre3@opengym.test
Mot de passe : Membre123!
Rôle : ROLE_MEMBER
```

## Routes API

| Méthode | URL                              | Rôle        | Fonction                       |
| ------- | -------------------------------- | ----------- | ------------------------------ |
| POST    | `/api/login`                     | Public      | Connexion et génération du JWT |
| GET     | `/api/seances`                   | Authentifié | Liste des séances à venir      |
| GET     | `/api/seances/{id}`              | Authentifié | Détail d'une séance            |
| POST    | `/api/seances`                   | ROLE_COACH  | Créer une séance               |
| PUT     | `/api/seances/{id}`              | ROLE_COACH  | Modifier une séance            |
| DELETE  | `/api/seances/{id}`              | ROLE_COACH  | Supprimer une séance           |
| GET     | `/api/seances/{id}/participants` | ROLE_COACH  | Voir les participants          |
| POST    | `/api/seances/{id}/reservations` | Authentifié | Réserver une séance            |
| DELETE  | `/api/reservations/{id}`         | Authentifié | Annuler une réservation        |
| GET     | `/api/me/reservations`           | Authentifié | Voir ses réservations          |

## Codes HTTP principaux

| Code | Signification                              |
| ---- | ------------------------------------------ |
| 200  | Requête réussie                            |
| 201  | Ressource créée                            |
| 401  | Authentification nécessaire ou JWT absent  |
| 403  | Accès interdit selon le rôle ou les droits |
| 404  | Ressource introuvable                      |
| 409  | Règle métier empêchant l'action            |

## Règles métier

Les règles métier concernant les réservations sont centralisées dans `ReservationService`.

### Séance complète

Un membre ne peut pas réserver une séance lorsqu'il n'y a plus de place disponible.

Réponse :

```text
409 Conflict
```

Message :

```json
{
    "message": "La séance est complète."
}
```

### Double réservation

Un membre ne peut pas réserver deux fois la même séance.

Réponse :

```text
409 Conflict
```

Message :

```json
{
    "message": "Vous avez déjà réservé cette séance."
}
```

### Séance passée

Une séance passée ne peut pas être réservée.

Réponse :

```text
409 Conflict
```

Message :

```json
{
    "message": "Impossible de réserver une séance passée."
}
```

### Annulation

Un membre ne peut annuler que ses propres réservations.

Une réservation appartenant à un autre membre retourne :

```text
403 Forbidden
```

Une réservation concernant une séance passée ne peut pas être annulée.

## Sécurité

L'API utilise :

* JWT pour l'authentification ;
* le hachage des mots de passe avec Symfony PasswordHasher ;
* des rôles `ROLE_COACH` et `ROLE_MEMBER` ;
* des routes protégées par Symfony Security ;
* des restrictions spécifiques aux routes du coach ;
* CORS configuré pour l'application React.

## CORS

L'application React utilise :

```text
http://localhost:5173
```

Les méthodes autorisées sont :

```text
GET
POST
PUT
DELETE
OPTIONS
```

Les headers `Content-Type` et `Authorization` sont autorisés.

## Tests réalisés avec Postman

Les principaux scénarios suivants ont été testés :

* connexion coach ;
* connexion membre ;
* accès sans JWT → `401` ;
* liste des séances ;
* détail d'une séance ;
* création d'une séance par un coach → `201` ;
* modification d'une séance par un coach ;
* suppression d'une séance par un coach ;
* consultation des participants ;
* réservation d'une séance ;
* consultation des réservations du membre ;
* annulation d'une réservation ;
* tentative d'action coach par un membre → `403` ;
* double réservation → `409` ;
* réservation d'une séance complète → `409` ;
* réservation d'une séance passée → `409` ;
* tentative d'annulation de la réservation d'un autre membre → `403`.

Les requêtes sont également regroupées dans le fichier :

```text
tests-api.http
```

## Données de test

Les fixtures créent :

* 1 coach ;
* 3 membres ;
* 6 séances ;
* plusieurs réservations.

La séance 5 est volontairement complète afin de tester la règle de réservation d'une séance sans place.

La séance 6 est volontairement passée afin de tester l'interdiction de réserver une séance passée.

## Vérifications techniques

Le projet a notamment été vérifié avec :

```bash
php bin/console doctrine:schema:validate
```

```bash
php bin/console lint:container
```

Les routes API peuvent être affichées avec :

```bash
php bin/console debug:router
```

## Difficultés rencontrées et solutions

### Authentification JWT

La configuration de Symfony Security et de LexikJWTAuthenticationBundle a été nécessaire pour protéger les routes API.

Les clés JWT ont été générées avec la commande :

```bash
php bin/console lexik:jwt:generate-keypair
```

### Tests JSON avec PowerShell

Les requêtes JSON envoyées depuis PowerShell ont nécessité une attention particulière à l'encodage UTF-8.

Un fichier JSON temporaire a été utilisé pour éviter les problèmes de guillemets et d'encodage.

### Sécurisation des routes

Les routes de création, modification, suppression et consultation des participants sont protégées avec `ROLE_COACH`.

Les règles liées aux réservations sont regroupées dans `ReservationService` afin de séparer la logique métier des contrôleurs.

## Architecture simplifiée

```text
Client
  │
  ▼
Controller
  │
  ├── Security / JWT
  │
  ├── Service métier
  │       │
  │       └── ReservationService
  │
  ▼
Doctrine ORM
  │
  ▼
MariaDB
```

## Fichier de tests

Les principales requêtes API sont disponibles dans :

```text
api/tests-api.http
```

Ce fichier permet de tester les fonctionnalités principales ainsi que plusieurs cas d'erreur et règles de sécurité.

