<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\UserModel;

class AuthController extends Controller
{
    public function registerForm(): void
    {
        if (Session::isLogged()) {
            $this->redirect('/profil');
        }
        $this->render('front/register', ['title' => 'Inscription', 'errors' => []]);
    }

    public function register(): void
    {
        $this->verifyCsrf();
        $users = new UserModel();

        $pseudo = $this->input('pseudo');
        $email = mb_strtolower($this->input('email'));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');
        $errors = [];

        if (!preg_match('/^[A-Za-z0-9_\-]{3,30}$/', $pseudo)) {
            $errors['pseudo'] = 'Le pseudo doit faire 3 à 30 caractères (lettres, chiffres, - et _).';
        } elseif ($users->findByPseudo($pseudo)) {
            $errors['pseudo'] = 'Ce pseudo est déjà pris.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
            $errors['email'] = 'Adresse e-mail invalide.';
        } elseif ($users->findByEmail($email)) {
            $errors['email'] = 'Un compte existe déjà avec cette adresse.';
        }

        $errors += $this->passwordErrors($password, $confirm);

        if ($errors) {
            $this->render('front/register', ['title' => 'Inscription', 'errors' => $errors]);
            return;
        }

        $id = $users->create($pseudo, $email, $password);
        Session::login($users->findById($id));
        Session::flash('success', 'Bienvenue ' . $pseudo . ', votre compte est créé.');
        $this->redirect('/');
    }

    public function loginForm(): void
    {
        if (Session::isLogged()) {
            $this->redirect('/');
        }
        $this->render('front/login', ['title' => 'Connexion', 'error' => null]);
    }

    public function login(): void
    {
        $this->verifyCsrf();
        $email = mb_strtolower($this->input('email'));
        $password = (string) ($_POST['password'] ?? '');

        $user = (new UserModel())->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            $this->render('front/login', ['title' => 'Connexion', 'error' => 'E-mail ou mot de passe incorrect.']);
            return;
        }
        if ((int) $user['is_banned'] === 1) {
            $this->render('front/login', ['title' => 'Connexion', 'error' => 'Ce compte a été suspendu.']);
            return;
        }

        Session::login($user);
        Session::flash('success', 'Vous êtes connecté.');
        $this->redirect(Session::canAccessAdmin() ? '/admin' : '/');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Session::logout();
        Session::start();
        Session::flash('success', 'Vous êtes déconnecté.');
        $this->redirect('/');
    }

    public function profile(): void
    {
        $this->requireLogin();
        $user = (new UserModel())->findById((int) Session::user()['id']);
        $this->render('front/profile', [
            'title'          => 'Mon profil',
            'user'           => $user,
            'profileErrors'  => [],
            'passwordErrors' => [],
        ]);
    }

    public function updateProfile(): void
    {
        $this->verifyCsrf();
        $this->requireLogin();
        $users = new UserModel();
        $me = $users->findById((int) Session::user()['id']);

        $pseudo = $this->input('pseudo');
        $email = mb_strtolower($this->input('email'));
        $errors = [];

        if (!preg_match('/^[A-Za-z0-9_\-]{3,30}$/', $pseudo)) {
            $errors['pseudo'] = 'Le pseudo doit faire 3 à 30 caractères (lettres, chiffres, - et _).';
        } else {
            $other = $users->findByPseudo($pseudo);
            if ($other && (int) $other['id'] !== (int) $me['id']) {
                $errors['pseudo'] = 'Ce pseudo est déjà pris.';
            }
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
            $errors['email'] = 'Adresse e-mail invalide.';
        } else {
            $other = $users->findByEmail($email);
            if ($other && (int) $other['id'] !== (int) $me['id']) {
                $errors['email'] = 'Cette adresse est déjà utilisée.';
            }
        }

        if ($errors) {
            $this->render('front/profile', [
                'title' => 'Mon profil', 'user' => $me, 'profileErrors' => $errors, 'passwordErrors' => [],
            ]);
            return;
        }

        $users->updateProfile((int) $me['id'], $pseudo, $email);
        Session::refresh($users->findById((int) $me['id']));
        Session::flash('success', 'Profil mis à jour.');
        $this->redirect('/profil');
    }

    public function updatePassword(): void
    {
        $this->verifyCsrf();
        $this->requireLogin();
        $users = new UserModel();
        $me = $users->findById((int) Session::user()['id']);

        $current = (string) ($_POST['current_password'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');
        $errors = [];

        if (!password_verify($current, $me['password'])) {
            $errors['current_password'] = 'Le mot de passe actuel est incorrect.';
        }
        $errors += $this->passwordErrors($password, $confirm);

        if ($errors) {
            $this->render('front/profile', [
                'title' => 'Mon profil', 'user' => $me, 'profileErrors' => [], 'passwordErrors' => $errors,
            ]);
            return;
        }

        $users->updatePassword((int) $me['id'], $password);
        Session::flash('success', 'Mot de passe modifié.');
        $this->redirect('/profil');
    }

    private function passwordErrors(string $password, string $confirm): array
    {
        $errors = [];
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            $errors['password'] = 'Au moins 8 caractères, avec une lettre et un chiffre.';
        } elseif ($password !== $confirm) {
            $errors['password_confirm'] = 'Les deux mots de passe ne correspondent pas.';
        }
        return $errors;
    }
}
