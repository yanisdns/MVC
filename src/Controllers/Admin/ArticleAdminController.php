<?php

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\ArticleModel;
use App\Models\CategoryModel;

class ArticleAdminController extends AdminController
{
    private const MAX_IMAGE_SIZE = 2097152; // 2 Mo
    private const MIME_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function index(): void
    {
        // Un auteur ne voit que ses propres articles
        $authorId = Session::isAdmin() ? null : (int) Session::user()['id'];
        $this->render('admin/articles/index', [
            'title'    => 'Articles',
            'articles' => (new ArticleModel())->all($authorId),
        ]);
    }

    public function create(): void
    {
        $this->renderForm('Nouvel article', null, []);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        [$data, $errors] = $this->collect(null);

        if ($errors) {
            $this->renderForm('Nouvel article', null, $errors);
            return;
        }

        $data['author_id'] = (int) Session::user()['id'];
        (new ArticleModel())->create($data);
        Session::flash('success', 'Article créé.');
        $this->redirect('/admin/articles');
    }

    public function edit(string $id): void
    {
        $article = $this->ownedArticle((int) $id);
        $this->renderForm('Modifier l\'article', $article, []);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $article = $this->ownedArticle((int) $id);
        [$data, $errors] = $this->collect($article);

        if ($errors) {
            $this->renderForm('Modifier l\'article', $article, $errors);
            return;
        }

        (new ArticleModel())->update((int) $article['id'], $data);

        // On supprime l'ancienne image si elle a ete remplacee
        if ($article['cover_image'] && $article['cover_image'] !== $data['cover_image']) {
            $this->deleteImage($article['cover_image']);
        }
        Session::flash('success', 'Article mis à jour.');
        $this->redirect('/admin/articles');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $article = $this->ownedArticle((int) $id);
        (new ArticleModel())->delete((int) $article['id']);
        if ($article['cover_image']) {
            $this->deleteImage($article['cover_image']);
        }
        Session::flash('success', 'Article supprimé.');
        $this->redirect('/admin/articles');
    }

    // Retourne l'article s'il existe et si l'utilisateur a le droit d'y toucher
    private function ownedArticle(int $id): array
    {
        $article = (new ArticleModel())->findById($id);
        if ($article === null) {
            $this->abort(404);
        }
        if (!Session::isAdmin() && (int) $article['author_id'] !== (int) Session::user()['id']) {
            $this->abort(403);
        }
        return $article;
    }

    private function renderForm(string $title, ?array $article, array $errors): void
    {
        $this->render('admin/articles/form', [
            'title'      => $title,
            'article'    => $article,
            'errors'     => $errors,
            'categories' => (new CategoryModel())->all(),
        ]);
    }

    // Valide les champs du formulaire et gere l'upload. Retourne [donnees, erreurs]
    private function collect(?array $existing): array
    {
        $articles = new ArticleModel();
        $errors = [];

        $title = $this->input('title');
        $content = $this->input('content');
        $status = $this->input('status');
        $categoryId = (int) $this->input('category_id');
        $slug = slugify($this->input('slug') !== '' ? $this->input('slug') : $title);
        $currentId = $existing ? (int) $existing['id'] : null;

        if ($title === '' || mb_strlen($title) > 200) {
            $errors['title'] = 'Le titre est obligatoire (200 caractères maximum).';
        }
        if ($content === '') {
            $errors['content'] = 'Le contenu ne peut pas être vide.';
        }
        if (!in_array($status, ['draft', 'published'], true)) {
            $errors['status'] = 'Statut invalide.';
        }
        if ($slug === '') {
            $errors['slug'] = 'Impossible de générer un slug à partir de ce titre.';
        } elseif ($articles->slugExists($slug, $currentId)) {
            $errors['slug'] = 'Ce slug est déjà utilisé par un autre article.';
        }

        $category = $categoryId > 0 ? (new CategoryModel())->find($categoryId) : null;
        if ($categoryId > 0 && $category === null) {
            $errors['category_id'] = 'Catégorie inconnue.';
        }

        $cover = $existing['cover_image'] ?? null;
        if (!empty($_POST['remove_cover'])) {
            $cover = null;
        }
        // On n'enregistre l'image que si le reste du formulaire est valide
        if (!$errors && isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = $this->saveImage($_FILES['cover']);
            if (is_array($upload)) {
                $errors['cover'] = $upload['error'];
            } else {
                $cover = $upload;
            }
        }

        return [[
            'title'       => $title,
            'slug'        => $slug,
            'content'     => $content,
            'status'      => $status,
            'category_id' => $category ? (int) $category['id'] : null,
            'cover_image' => $cover,
        ], $errors];
    }

    // Retourne le nom du fichier enregistre, ou un tableau ['error' => ...] en cas de probleme
    private function saveImage(array $file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Le fichier n\'a pas pu être envoyé.'];
        }
        if ($file['size'] > self::MAX_IMAGE_SIZE) {
            return ['error' => 'L\'image dépasse 2 Mo.'];
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Fichier invalide.'];
        }

        // Le type est lu dans le contenu du fichier, pas dans le nom ni dans le type envoye par le navigateur
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::MIME_TYPES[$mime]) || @getimagesize($file['tmp_name']) === false) {
            return ['error' => 'Format accepté : JPEG, PNG ou WebP.'];
        }

        $name = bin2hex(random_bytes(12)) . '.' . self::MIME_TYPES[$mime];
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name)) {
            return ['error' => 'Impossible d\'enregistrer l\'image sur le serveur.'];
        }
        return $name;
    }

    private function deleteImage(string $name): void
    {
        $path = UPLOAD_DIR . '/' . basename($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
