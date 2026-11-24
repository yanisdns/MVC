<?php

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\UserModel;

class UserAdminController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $this->render('admin/users/index', [
            'title' => 'Utilisateurs',
            'users' => (new UserModel())->all(),
        ]);
    }

    public function changeRole(string $id): void
    {
        $this->verifyCsrf();
        $user = $this->targetUser((int) $id);
        $role = $this->input('role');

        if (!in_array($role, ['USER', 'AUTHOR', 'ADMIN'], true)) {
            Session::flash('error', 'Rôle invalide.');
        } else {
            (new UserModel())->setRole((int) $user['id'], $role);
            Session::flash('success', 'Rôle de ' . $user['pseudo'] . ' modifié.');
        }
        $this->redirect('/admin/utilisateurs');
    }

    public function toggleBan(string $id): void
    {
        $this->verifyCsrf();
        $user = $this->targetUser((int) $id);
        $ban = (int) $user['is_banned'] === 0;
        (new UserModel())->setBanned((int) $user['id'], $ban);
        Session::flash('success', $user['pseudo'] . ($ban ? ' est banni.' : ' est de nouveau actif.'));
        $this->redirect('/admin/utilisateurs');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $user = $this->targetUser((int) $id);
        // La suppression d'un utilisateur supprime aussi ses articles et commentaires (cle etrangere en cascade)
        (new UserModel())->delete((int) $user['id']);
        Session::flash('success', 'Compte de ' . $user['pseudo'] . ' supprimé.');
        $this->redirect('/admin/utilisateurs');
    }

    // Charge l'utilisateur vise et interdit de modifier son propre compte depuis cette page
    private function targetUser(int $id): array
    {
        $user = (new UserModel())->findById($id);
        if ($user === null) {
            $this->abort(404);
        }
        if ((int) $user['id'] === (int) Session::user()['id']) {
            Session::flash('error', 'Vous ne pouvez pas modifier votre propre compte ici.');
            $this->redirect('/admin/utilisateurs');
        }
        return $user;
    }
}
