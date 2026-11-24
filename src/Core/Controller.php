<?php

namespace App\Core;

use App\Controllers\ErrorController;

abstract class Controller
{
    protected string $layout = 'front';

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $flashes = Session::pullFlashes();

        ob_start();
        require VIEWS . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEWS . '/layouts/' . $this->layout . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function input(string $key): string
    {
        return trim((string) ($_POST[$key] ?? ''));
    }

    // A appeler au debut de chaque action qui traite un formulaire POST
    protected function verifyCsrf(): void
    {
        if (!Csrf::check($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            $this->render('errors/csrf', ['title' => 'Session expirée']);
            exit;
        }
    }

    protected function requireLogin(): void
    {
        if (!Session::isLogged()) {
            Session::flash('error', 'Connectez-vous pour accéder à cette page.');
            $this->redirect('/connexion');
        }
    }

    protected function abort(int $code): void
    {
        $controller = new ErrorController();
        if ($code === 403) {
            $controller->forbidden();
        } else {
            $controller->notFound();
        }
        exit;
    }
}
