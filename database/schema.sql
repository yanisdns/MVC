-- Schema et jeu de donnees de demonstration du blog
-- Importer ce fichier dans MySQL ou MariaDB suffit pour tout recreer.

CREATE DATABASE IF NOT EXISTS mon_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mon_blog;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    pseudo VARCHAR(30) NOT NULL,
    email VARCHAR(120) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('USER', 'AUTHOR', 'ADMIN') NOT NULL DEFAULT 'USER',
    is_banned TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_pseudo (pseudo),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(60) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE articles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    content TEXT NOT NULL,
    cover_image VARCHAR(100) DEFAULT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    category_id INT UNSIGNED DEFAULT NULL,
    author_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    published_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_articles_slug (slug),
    KEY idx_articles_status_date (status, published_at),
    CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_articles_author FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    article_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_comments_article (article_id, status),
    CONSTRAINT fk_comments_article FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comptes de test : admin@blog.fr / Admin1234!   auteur@blog.fr / Auteur1234!   membre@blog.fr / Membre1234!
INSERT INTO users (id, pseudo, email, password, role, created_at) VALUES
(1, 'admin', 'admin@blog.fr', '$2b$10$.T1a7c5p94umfIxp18GXwOVqVE9WOP18dmqzrWQHXMl3soi0Ltx3K', 'ADMIN', '2026-01-10 09:00:00'),
(2, 'camille', 'auteur@blog.fr', '$2b$10$ua6OQMEyDdddKE7G5qtVBOnQaIgRuGhrcEAJ33t1KD8CgRKNbSEVa', 'AUTHOR', '2026-01-12 14:30:00'),
(3, 'julien', 'membre@blog.fr', '$2b$10$reib0icLZE7xNZpDsye3juf0AaTUQ6XzCBUe1TqbCl3p5dEgu7B5K', 'USER', '2026-02-03 18:45:00');

INSERT INTO categories (id, name, slug) VALUES
(1, 'PHP', 'php'),
(2, 'Base de données', 'base-de-donnees'),
(3, 'CSS et intégration', 'css-et-integration'),
(4, 'Sécurité', 'securite'),
(5, 'Outils', 'outils');

INSERT INTO articles (title, slug, content, status, category_id, author_id, created_at, published_at) VALUES
('Pourquoi je n''utilise plus de require partout', 'pourquoi-je-nutilise-plus-de-require-partout', 'Pendant longtemps, mes fichiers PHP commençaient par une dizaine de lignes require_once. Ça marchait, mais chaque nouvelle classe m''obligeait à repenser les chemins, et j''oubliais régulièrement un fichier.

## L''autoloading

Avec spl_autoload_register, PHP va chercher la classe tout seul au moment où on en a besoin. Il suffit de respecter une convention : le namespace correspond au chemin du fichier.

- App\\Models\\ArticleModel se trouve dans src/Models/ArticleModel.php
- Un fichier par classe, avec le même nom que la classe
- Un seul require dans le point d''entrée

## Ce que ça change au quotidien

Je crée une classe, je la nomme correctement, et je l''utilise. Plus de liste à maintenir. C''est un petit détail, mais on gagne du temps sur chaque projet.', 'published', 1, 1, '2026-03-02 10:00:00', '2026-03-02 10:00:00'),
('Construire un routeur en trente lignes', 'construire-un-routeur-en-trente-lignes', 'Un routeur, c''est surtout une table de correspondance entre une URL et une méthode de contrôleur. On peut s''en passer en utilisant un framework, mais en écrire un soi-même aide à comprendre ce qui se passe derrière.

## Le principe

On enregistre des routes sous la forme d''un motif, par exemple /article/{slug}, et d''un couple classe et méthode. Au moment de traiter la requête, on transforme chaque motif en expression régulière et on garde le premier qui correspond.

Les parties entre accolades deviennent des groupes nommés, qu''on passe ensuite en paramètres à la méthode du contrôleur.

## Les pièges

Il faut penser à retirer le dossier de base quand le site n''est pas à la racine du serveur, et à renvoyer une vraie erreur 404 quand rien ne correspond. Le reste est assez mécanique.', 'published', 1, 1, '2026-03-09 08:30:00', '2026-03-09 08:30:00'),
('Les requêtes préparées expliquées simplement', 'les-requetes-preparees-expliquees-simplement', 'Une injection SQL arrive quand on colle une valeur saisie par l''utilisateur directement dans une requête. Si cette valeur contient du SQL, la base de données l''exécute comme n''importe quelle autre instruction.

## Séparer la requête et les données

Avec une requête préparée, on envoie d''abord la structure de la requête avec des marqueurs à la place des valeurs. Les valeurs arrivent ensuite séparément, et la base sait qu''il s''agit uniquement de données.

## Avec PDO

On écrit prepare, puis execute avec le tableau des paramètres. C''est tout. Il n''y a aucune raison de construire une requête par concaténation, même pour un simple identifiant dans l''URL.

Un point à retenir : les requêtes préparées protègent contre l''injection SQL, pas contre le XSS. Ce sont deux problèmes différents, qui demandent deux protections différentes.', 'published', 4, 2, '2026-03-16 17:15:00', '2026-03-16 17:15:00'),
('Mettre en page avec CSS Grid sans se prendre la tête', 'mettre-en-page-avec-css-grid', 'Pendant des années j''ai fait mes mises en page avec des flottants, puis avec Flexbox. Grid m''a fait peur au début, à cause de la quantité de propriétés. En pratique, on n''en utilise que quelques-unes.

## Deux colonnes, une qui s''adapte

Pour une liste d''articles avec une colonne latérale, il suffit de déclarer deux colonnes : une qui prend la place disponible et une de largeur fixe. Sur petit écran, on repasse à une seule colonne avec une media query.

## Quand préférer Flexbox

Flexbox reste plus simple pour aligner des éléments sur une seule ligne, comme un menu ou une barre de recherche. Grid est plus à l''aise dès qu''on pense en lignes et en colonnes en même temps.

Les deux ne s''opposent pas, on les mélange sans problème dans la même page.', 'published', 3, 2, '2026-03-23 11:20:00', '2026-03-23 11:20:00'),
('Se protéger des failles XSS dans ses vues', 'se-proteger-des-failles-xss', 'Une faille XSS permet à quelqu''un d''injecter du JavaScript dans une page vue par d''autres utilisateurs. Sur un blog, l''endroit classique, c''est la zone de commentaires.

## La règle

Tout ce qui vient de la base de données ou d''un formulaire doit être échappé au moment de l''affichage. En PHP, c''est le rôle de htmlspecialchars, avec l''option ENT_QUOTES et l''encodage UTF-8.

## Une petite fonction pour ne pas oublier

Plutôt que de réécrire l''appel complet à chaque fois, je définis une fonction e() et je l''utilise partout dans les vues. Si je vois une variable affichée sans e(), je sais que quelque chose cloche.

Pour le contenu des articles, j''échappe d''abord, puis je transforme quelques conventions simples en HTML. Comme ça, aucun balisage saisi n''est jamais interprété.', 'published', 4, 1, '2026-04-01 09:45:00', '2026-04-01 09:45:00'),
('Modéliser un blog : quatre tables suffisent', 'modeliser-un-blog-quatre-tables', 'Quand on commence à concevoir une base de données, on a tendance à vouloir tout prévoir. Pour un blog, quatre tables font déjà un bon point de départ : users, categories, articles et comments.

## Les relations

Un article appartient à un auteur et à une catégorie. Un commentaire appartient à un article et à un utilisateur. Ce sont des relations un à plusieurs, qu''on traduit par des clés étrangères.

## Les suppressions

Il faut décider ce qui arrive quand on supprime quelque chose. Si on retire une catégorie, les articles restent mais sans catégorie. Si on retire un article, ses commentaires n''ont plus de sens et partent avec lui. Ces choix se traduisent par ON DELETE SET NULL ou ON DELETE CASCADE.

Avoir réfléchi à ça dès le départ évite des surprises plus tard.', 'published', 2, 2, '2026-04-08 16:00:00', '2026-04-08 16:00:00'),
('Git : des commits petits et lisibles', 'git-des-commits-petits-et-lisibles', 'Un historique Git sert à retrouver pourquoi une modification a été faite. Un commit qui s''appelle « modifs » ne répond à aucune question.

## Mes habitudes

- Un commit par idée : une fonctionnalité, une correction, un renommage
- Un message à l''impératif ou au passé, mais toujours le même style
- Un titre court, et quelques lignes de détail si le changement n''est pas évident

## Ce que j''évite

Les gros commits qui mélangent trois sujets, et les fichiers de configuration avec des mots de passe. Pour ça, un fichier d''exemple versionné et le vrai fichier dans le .gitignore.

Au début c''est contraignant. Au bout de quelques semaines, c''est un réflexe.', 'published', 5, 1, '2026-04-15 13:10:00', '2026-04-15 13:10:00'),
('Envoyer une image en toute sécurité avec PHP', 'envoyer-une-image-en-toute-securite', 'Permettre l''envoi de fichiers, c''est ouvrir une porte sur le serveur. Il faut donc vérifier plusieurs choses avant d''accepter un fichier.

## Les vérifications

- La taille, pour éviter les fichiers énormes
- Le type réel du fichier, lu dans son contenu avec finfo, et pas seulement l''extension
- Un contrôle supplémentaire avec getimagesize pour s''assurer que c''est bien une image

## Le nom du fichier

Je ne garde jamais le nom d''origine. Je génère un nom aléatoire et je choisis moi-même l''extension à partir du type détecté. Ça évite les collisions et les noms piégés.

Enfin, dans le dossier des envois, un fichier .htaccess interdit l''exécution de scripts, au cas où quelque chose passerait entre les mailles.', 'published', 4, 2, '2026-04-22 19:30:00', '2026-04-22 19:30:00'),
('Hacher les mots de passe correctement', 'hacher-les-mots-de-passe-correctement', 'On ne stocke jamais un mot de passe en clair, ni avec md5 ou sha1. Ces fonctions sont faites pour être rapides, ce qui est exactement ce qu''on ne veut pas ici.

## Ce que PHP propose

password_hash produit un hachage lent, avec un sel généré automatiquement. password_verify compare un mot de passe saisi avec le hachage enregistré. On n''a rien d''autre à gérer.

## Garder le code simple

Le hachage se fait dans le modèle au moment de l''enregistrement, et la vérification dans le contrôleur de connexion. Le message d''erreur reste le même que l''e-mail soit inconnu ou le mot de passe faux, pour ne pas révéler quels comptes existent.', 'published', 4, 1, '2026-05-04 08:00:00', '2026-05-04 08:00:00'),
('Brouillon : idées pour la suite', 'brouillon-idees-pour-la-suite', 'Notes en vrac : un article sur les tests, un autre sur le déploiement, peut-être un retour sur la pagination.', 'draft', 5, 1, '2026-05-10 10:00:00', NULL);

INSERT INTO comments (user_id, article_id, content, status, created_at) VALUES
(3, 1, 'Merci, j''avais exactement ce problème avec mes chemins relatifs.', 'approved', '2026-03-03 12:10:00'),
(3, 3, 'Très clair. Je pensais que prepare suffisait à tout régler, pour le XSS je ne savais pas.', 'approved', '2026-03-17 20:05:00'),
(2, 3, 'C''est le message que j''aurais voulu lire il y a un an.', 'approved', '2026-03-18 09:40:00'),
(3, 5, 'Et pour les champs qui contiennent du HTML légitime, on fait comment ?', 'pending', '2026-04-02 21:15:00'),
(3, 7, 'Je mets ça en pratique dès ce soir.', 'approved', '2026-04-16 08:55:00');
