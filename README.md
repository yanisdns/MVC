# Carnet Web

Un blog dynamique écrit en PHP, sans framework, avec une architecture MVC faite maison. Le projet a été réalisé dans le cadre de l'atelier "Création d'un blog complet en architecture MVC".

Le site public (lecture, recherche, commentaires, inscription) utilise du CSS natif. L'administration utilise Bootstrap 5.

## Fonctionnalités

Partie publique :
- liste des derniers articles avec pagination (6 par page)
- filtre par catégorie et recherche dans le titre et le contenu
- page d'article avec commentaires (réservés aux membres connectés, validés par un administrateur)
- inscription, connexion, déconnexion
- profil : modification du pseudo, de l'e-mail et du mot de passe

Administration (rôles ADMIN et AUTHOR) :
- tableau de bord avec les chiffres clés
- articles : liste, création, modification, suppression, brouillon ou publié, image de couverture
- catégories : création, renommage, suppression (ADMIN)
- commentaires : approuver, rejeter, supprimer (ADMIN)
- utilisateurs : changer le rôle, bannir, supprimer (ADMIN)

Un auteur (AUTHOR) ne voit et ne modifie que ses propres articles. Ses commentaires sont publiés directement.

## Prérequis

- PHP 8.2 ou plus, avec les extensions pdo_mysql et fileinfo
- MySQL ou MariaDB
- Apache avec mod_rewrite (XAMPP, Laragon, MAMP...), ou le serveur intégré de PHP

## Installation

1. Récupérer le projet :

   ```
   git clone <adresse-du-depot> mon-blog-mvc
   cd mon-blog-mvc
   ```

2. Créer la base et les données de démonstration en important le script SQL :

   ```
   mysql -u root -p < database/schema.sql
   ```

   Le script crée la base `mon_blog`. On peut aussi l'importer depuis phpMyAdmin.

3. Copier le fichier de configuration et l'adapter :

   ```
   cp config/database.example.php config/database.php
   ```

   Puis renseigner l'hôte, le nom de la base, l'utilisateur et le mot de passe dans `config/database.php`. Ce fichier est ignoré par Git.

4. Lancer le site. Le dossier `public/` doit être la racine du serveur web.

   Avec le serveur intégré de PHP :

   ```
   php -S localhost:8000 -t public
   ```

   Le site est alors disponible sur http://localhost:8000.

   Avec Apache, pointer le DocumentRoot (ou un VirtualHost) vers le dossier `public/`. Le fichier `.htaccess` s'occupe de la réécriture d'URL.

5. Vérifier que le dossier `public/assets/uploads/` est accessible en écriture par le serveur web (nécessaire pour les images de couverture).

## Comptes de test

| Rôle | E-mail | Mot de passe |
| --- | --- | --- |
| Administrateur | admin@blog.fr | Admin1234! |
| Auteur | auteur@blog.fr | Auteur1234! |
| Membre | membre@blog.fr | Membre1234! |

Ces comptes sont créés par `database/schema.sql`. À supprimer ou à modifier avant toute mise en ligne.

## Structure du projet

```
config/         configuration de la base (modèle versionné, vrai fichier ignoré)
database/       schema.sql : tables et données de démonstration
public/         racine web : index.php, .htaccess, assets (css, js, uploads)
src/Core/       Router, Database (PDO), Model, Controller, Session, Csrf
src/Models/     accès aux données (ArticleModel, UserModel, CategoryModel, CommentModel)
src/Controllers contrôleurs du site public et de l'administration (dossier Admin)
src/Views/      vues PHP : front, admin, layouts, errors
src/routes.php  liste des routes
```

Le fonctionnement est le suivant : toutes les requêtes arrivent sur `public/index.php`, qui charge l'autoloader et le routeur. Le routeur choisit un contrôleur, le contrôleur appelle les modèles puis affiche une vue dans un gabarit.

## Sécurité

- requêtes préparées PDO partout, aucune requête SQL en dehors des modèles
- échappement des données affichées avec la fonction `e()` (htmlspecialchars)
- jeton CSRF sur tous les formulaires POST (connexion, inscription, suppressions, modération...)
- mots de passe avec `password_hash()` et `password_verify()`
- accès à `/admin` contrôlé par le rôle, erreur 403 sinon
- upload d'images : taille limitée, type vérifié dans le contenu du fichier, nom aléatoire, exécution de scripts interdite dans le dossier d'envoi
- identifiants de base de données hors du dépôt Git

## Captures d'écran

À ajouter dans un dossier `docs/` une fois le projet installé : page d'accueil, page d'un article, tableau de bord, formulaire d'article.
