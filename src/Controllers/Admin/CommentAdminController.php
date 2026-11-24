<?php

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\CommentModel;

class CommentAdminController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $filter = $_GET['statut'] ?? '';
        if (!in_array($filter, ['pending', 'approved', 'rejected'], true)) {
            $filter = '';
        }
        $this->render('admin/comments/index', [
            'title'    => 'Commentaires',
            'comments' => (new CommentModel())->all($filter ?: null),
            'filter'   => $filter,
        ]);
    }

    public function approve(string $id): void
    {
        $this->changeStatus((int) $id, 'approved', 'Commentaire approuvé.');
    }

    public function reject(string $id): void
    {
        $this->changeStatus((int) $id, 'rejected', 'Commentaire rejeté.');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        (new CommentModel())->delete((int) $id);
        Session::flash('success', 'Commentaire supprimé.');
        $this->redirect('/admin/commentaires');
    }

    private function changeStatus(int $id, string $status, string $message): void
    {
        $this->verifyCsrf();
        (new CommentModel())->setStatus($id, $status);
        Session::flash('success', $message);
        $this->redirect('/admin/commentaires');
    }
}
