<?php

namespace App\Controllers\Admin;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\CommentModel;
use App\Models\UserModel;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $articles = new ArticleModel();
        $comments = new CommentModel();

        $this->render('admin/dashboard', [
            'title' => 'Tableau de bord',
            'stats' => [
                'articles'         => $articles->count(),
                'published'        => $articles->countPublished(),
                'comments'         => $comments->count(),
                'pending_comments' => $comments->countPending(),
                'users'            => (new UserModel())->count(),
                'categories'       => (new CategoryModel())->count(),
            ],
        ]);
    }
}
