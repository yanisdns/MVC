<?php
// Toutes les routes de l'application. $router est cree dans public/index.php

use App\Controllers\HomeController;
use App\Controllers\ArticleController;
use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ArticleAdminController;
use App\Controllers\Admin\CategoryAdminController;
use App\Controllers\Admin\CommentAdminController;
use App\Controllers\Admin\UserAdminController;

// Partie publique
$router->get('/', [HomeController::class, 'index']);
$router->get('/categorie/{slug}', [HomeController::class, 'category']);
$router->get('/article/{slug}', [ArticleController::class, 'show']);
$router->post('/article/{slug}/commentaire', [ArticleController::class, 'comment']);

// Authentification et profil
$router->get('/inscription', [AuthController::class, 'registerForm']);
$router->post('/inscription', [AuthController::class, 'register']);
$router->get('/connexion', [AuthController::class, 'loginForm']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->post('/deconnexion', [AuthController::class, 'logout']);
$router->get('/profil', [AuthController::class, 'profile']);
$router->post('/profil', [AuthController::class, 'updateProfile']);
$router->post('/profil/mot-de-passe', [AuthController::class, 'updatePassword']);

// Administration
$router->get('/admin', [DashboardController::class, 'index']);

$router->get('/admin/articles', [ArticleAdminController::class, 'index']);
$router->get('/admin/articles/creer', [ArticleAdminController::class, 'create']);
$router->post('/admin/articles/creer', [ArticleAdminController::class, 'store']);
$router->get('/admin/articles/{id}/modifier', [ArticleAdminController::class, 'edit']);
$router->post('/admin/articles/{id}/modifier', [ArticleAdminController::class, 'update']);
$router->post('/admin/articles/{id}/supprimer', [ArticleAdminController::class, 'delete']);

$router->get('/admin/categories', [CategoryAdminController::class, 'index']);
$router->post('/admin/categories/creer', [CategoryAdminController::class, 'store']);
$router->get('/admin/categories/{id}/modifier', [CategoryAdminController::class, 'edit']);
$router->post('/admin/categories/{id}/modifier', [CategoryAdminController::class, 'update']);
$router->post('/admin/categories/{id}/supprimer', [CategoryAdminController::class, 'delete']);

$router->get('/admin/commentaires', [CommentAdminController::class, 'index']);
$router->post('/admin/commentaires/{id}/approuver', [CommentAdminController::class, 'approve']);
$router->post('/admin/commentaires/{id}/rejeter', [CommentAdminController::class, 'reject']);
$router->post('/admin/commentaires/{id}/supprimer', [CommentAdminController::class, 'delete']);

$router->get('/admin/utilisateurs', [UserAdminController::class, 'index']);
$router->post('/admin/utilisateurs/{id}/role', [UserAdminController::class, 'changeRole']);
$router->post('/admin/utilisateurs/{id}/bannir', [UserAdminController::class, 'toggleBan']);
$router->post('/admin/utilisateurs/{id}/supprimer', [UserAdminController::class, 'delete']);
