<h1 class="h3 mb-4">Tableau de bord</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Articles</div>
            <div class="fs-2"><?= (int) $stats['articles'] ?></div>
            <div class="small text-muted"><?= (int) $stats['published'] ?> publiés, <?= (int) ($stats['articles'] - $stats['published']) ?> brouillons</div>
        </div></div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Commentaires</div>
            <div class="fs-2"><?= (int) $stats['comments'] ?></div>
            <div class="small <?= $stats['pending_comments'] > 0 ? 'text-danger' : 'text-muted' ?>">
                <?= (int) $stats['pending_comments'] ?> en attente de modération
            </div>
        </div></div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Utilisateurs</div>
            <div class="fs-2"><?= (int) $stats['users'] ?></div>
        </div></div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-muted small">Catégories</div>
            <div class="fs-2"><?= (int) $stats['categories'] ?></div>
        </div></div>
    </div>
</div>

<a class="btn btn-primary" href="<?= url('/admin/articles/creer') ?>">Écrire un article</a>
