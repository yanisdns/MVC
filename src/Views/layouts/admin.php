<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Administration') ?> - Administration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="bg-light">
<?php $me = current_user(); ?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= url('/admin') ?>">Carnet Web - Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('/admin') ?>">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/admin/articles') ?>">Articles</a></li>
                <?php if ($me['role'] === 'ADMIN'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/admin/categories') ?>">Catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/admin/commentaires') ?>">Commentaires</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/admin/utilisateurs') ?>">Utilisateurs</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text me-3"><?= e($me['pseudo']) ?> (<?= e(role_label($me['role'])) ?>)</span>
            <a class="btn btn-outline-light btn-sm me-2" href="<?= url('/') ?>">Voir le site</a>
            <form action="<?= url('/deconnexion') ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-secondary btn-sm">Déconnexion</button>
            </form>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php foreach ($flashes as $f): ?>
        <div class="alert alert-<?= $f['type'] === 'error' ? 'danger' : 'success' ?>" role="alert"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
