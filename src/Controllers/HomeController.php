<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ArticleModel;
use App\Models\CategoryModel;

class HomeController extends Controller
{
    private const PER_PAGE = 6;

    public function index(): void
    {
        $this->listing(null);
    }

    public function category(string $slug): void
    {
        $category = (new CategoryModel())->findBySlug($slug);
        if ($category === null) {
            $this->abort(404);
        }
        $this->listing($category);
    }

    private function listing(?array $category): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim((string) ($_GET['q'] ?? ''));

        $result = (new ArticleModel())->paginatePublished(
            $page,
            self::PER_PAGE,
            $category['slug'] ?? null,
            $search
        );

        $pages = max(1, (int) ceil($result['total'] / self::PER_PAGE));
        if ($page > $pages) {
            $this->abort(404);
        }

        if ($category !== null) {
            $title = $category['name'];
        } elseif ($search !== '') {
            $title = 'Recherche : ' . $search;
        } else {
            $title = 'Accueil';
        }

        $this->render('front/home', [
            'title'      => $title,
            'articles'   => $result['items'],
            'total'      => $result['total'],
            'page'       => $page,
            'pages'      => $pages,
            'search'     => $search,
            'category'   => $category,
            'categories' => (new CategoryModel())->allWithCount(),
        ]);
    }
}
