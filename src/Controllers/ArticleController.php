<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ArticleModel;
use App\Models\CommentModel;

class ArticleController extends Controller
{
    public function show(string $slug): void
    {
        $article = (new ArticleModel())->findPublishedBySlug($slug);
        if ($article === null) {
            $this->abort(404);
        }

        $this->render('front/article', [
            'title'    => $article['title'],
            'article'  => $article,
            'comments' => (new CommentModel())->approvedForArticle((int) $article['id']),
        ]);
    }

    public function comment(string $slug): void
    {
        $this->verifyCsrf();
        $this->requireLogin();

        $article = (new ArticleModel())->findPublishedBySlug($slug);
        if ($article === null) {
            $this->abort(404);
        }

        $content = $this->input('content');
        if ($content === '' || mb_strlen($content) > 1000) {
            Session::flash('error', 'Le commentaire doit contenir entre 1 et 1000 caractères.');
            $this->redirect('/article/' . $slug . '#commentaires');
        }

        // Les administrateurs et auteurs sont publies directement, les autres passent par la moderation
        $user = Session::user();
        $status = Session::canAccessAdmin() ? 'approved' : 'pending';
        (new CommentModel())->create((int) $article['id'], (int) $user['id'], $content, $status);

        if ($status === 'pending') {
            Session::flash('success', 'Merci, votre commentaire sera visible après validation.');
        } else {
            Session::flash('success', 'Commentaire publié.');
        }
        $this->redirect('/article/' . $slug . '#commentaires');
    }
}
