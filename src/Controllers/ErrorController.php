<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Page introuvable']);
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->render('errors/403', ['title' => 'Accès refusé']);
    }
}
