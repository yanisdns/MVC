<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Carnet Web') ?> - Carnet Web</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="wrap header-inner">
        <a class="site-name" href="<?= url('/') ?>">Carnet Web</a>
        <nav class="main-nav">
            <a href="<?= url('/') ?>">Articles</a>
            <?php if ($user = current_user()): ?>
                <?php if (in_array($user['role'], ['ADMIN', 'AUTHOR'], true)): ?>
                    <a href="<?= url('/admin') ?>">Administration</a>
                <?php endif; ?>
                <a href="<?= url('/profil') ?>"><?= e($user['pseudo']) ?></a>
                <form action="<?= url('/deconnexion') ?>" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-button">Déconnexion</button>
                </form>
            <?php else: ?>
                <a href="<?= url('/connexion') ?>">Connexion</a>
                <a href="<?= url('/inscription') ?>" class="nav-signup">Inscription</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="wrap">
    <?php foreach ($flashes as $f): ?>
        <p class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></p>
    <?php endforeach; ?>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="wrap">
        <p>Carnet Web. Notes sur le développement web, écrites au fil de l'eau.</p>
    </div>
</footer>
</body>
</html>
