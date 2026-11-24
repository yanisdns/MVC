<?php

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\CategoryModel;

class CategoryAdminController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $this->render('admin/categories/index', [
            'title'      => 'Catégories',
            'categories' => (new CategoryModel())->allWithTotals(),
            'error'      => null,
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $categories = new CategoryModel();
        $name = $this->input('name');
        $slug = slugify($name);
        $error = $this->validate($categories, $name, $slug, null);

        if ($error) {
            $this->render('admin/categories/index', [
                'title' => 'Catégories', 'categories' => $categories->allWithTotals(), 'error' => $error,
            ]);
            return;
        }
        $categories->create($name, $slug);
        Session::flash('success', 'Catégorie ajoutée.');
        $this->redirect('/admin/categories');
    }

    public function edit(string $id): void
    {
        $category = (new CategoryModel())->find((int) $id);
        if ($category === null) {
            $this->abort(404);
        }
        $this->render('admin/categories/form', ['title' => 'Renommer la catégorie', 'category' => $category, 'error' => null]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $categories = new CategoryModel();
        $category = $categories->find((int) $id);
        if ($category === null) {
            $this->abort(404);
        }

        $name = $this->input('name');
        $slug = slugify($name);
        $error = $this->validate($categories, $name, $slug, (int) $category['id']);

        if ($error) {
            $this->render('admin/categories/form', ['title' => 'Renommer la catégorie', 'category' => $category, 'error' => $error]);
            return;
        }
        $categories->update((int) $category['id'], $name, $slug);
        Session::flash('success', 'Catégorie renommée.');
        $this->redirect('/admin/categories');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $categories = new CategoryModel();
        if ($categories->find((int) $id) === null) {
            $this->abort(404);
        }
        // Les articles de cette categorie restent en base, sans categorie (ON DELETE SET NULL)
        $categories->delete((int) $id);
        Session::flash('success', 'Catégorie supprimée.');
        $this->redirect('/admin/categories');
    }

    private function validate(CategoryModel $categories, string $name, string $slug, ?int $exceptId): ?string
    {
        if ($name === '' || mb_strlen($name) > 60 || $slug === '') {
            return 'Le nom est obligatoire (60 caractères maximum).';
        }
        if ($categories->slugExists($slug, $exceptId)) {
            return 'Une catégorie portant ce nom existe déjà.';
        }
        return null;
    }
}
