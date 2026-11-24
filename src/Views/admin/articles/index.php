<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Articles</h1>
    <a class="btn btn-primary" href="<?= url('/admin/articles/creer') ?>">Nouvel article</a>
</div>

<?php if (!$articles): ?>
    <p class="text-muted">Aucun article pour le moment.</p>
<?php else: ?>
<div class="table-responsive">
<table class="table table-striped align-middle bg-white">
    <thead>
        <tr><th>Titre</th><th>Catégorie</th><th>Auteur</th><th>Statut</th><th>Créé le</th><th class="text-end">Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($articles as $a): ?>
        <tr>
            <td><?= e($a['title']) ?></td>
            <td><?= e($a['category_name'] ?? 'Aucune') ?></td>
            <td><?= e($a['author']) ?></td>
            <td>
                <?php if ($a['status'] === 'published'): ?>
                    <span class="badge text-bg-success">Publié</span>
                <?php else: ?>
                    <span class="badge text-bg-secondary">Brouillon</span>
                <?php endif; ?>
            </td>
            <td><?= e(date_fr($a['created_at'])) ?></td>
            <td class="text-end text-nowrap">
                <?php if ($a['status'] === 'published'): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url('/article/' . $a['slug']) ?>">Voir</a>
                <?php endif; ?>
                <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/articles/' . $a['id'] . '/modifier') ?>">Modifier</a>
                <form action="<?= url('/admin/articles/' . $a['id'] . '/supprimer') ?>" method="post" class="d-inline"
                      data-confirm="Supprimer l'article « <?= e($a['title']) ?> » ? Cette action est définitive.">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
