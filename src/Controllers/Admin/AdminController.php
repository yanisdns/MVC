<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;

// Classe mere des controleurs du back-office : verifie l'acces et change de gabarit
abstract class AdminController extends Controller
{
    protected string $layout = 'admin';

    public function __construct()
    {
        if (!Session::isLogged()) {
            Session::flash('error', 'Connectez-vous pour accéder à l\'administration.');
            $this->redirect('/connexion');
        }
        if (!Session::canAccessAdmin()) {
            $this->abort(403);
        }
    }

    // Pour les pages reservees aux administrateurs (categories, utilisateurs)
    protected function requireAdmin(): void
    {
        if (!Session::isAdmin()) {
            $this->abort(403);
        }
    }
}
