<h1 class="h3 mb-4">Catégories</h1>

<form action="<?= url('/admin/categories/creer') ?>" method="post" class="row g-2 mb-4" novalidate>
    <?= csrf_field() ?>
    <div class="col-sm-6 col-md-4">
        <label for="name" class="visually-hidden">Nom de la catégorie</label>
        <input type="text" id="name" name="name" class="form-control <?= $error ? 'is-invalid' : '' ?>" placeholder="Nom de la nouvelle catégorie" required>
        <?php if ($error): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
    </div>
    <div class="col-auto"><button type="submit" class="btn btn-primary">Ajouter</button></div>
</form>

<div class="table-responsive">
<table class="table table-striped align-middle bg-white">
    <thead><tr><th>Nom</th><th>Slug</th><th>Articles</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($categories as $c): ?>
        <tr>
            <td><?= e($c['name']) ?></td>
            <td class="text-muted"><?= e($c['slug']) ?></td>
            <td><?= (int) $c['nb_articles'] ?></td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/categories/' . $c['id'] . '/modifier') ?>">Renommer</a>
                <form action="<?= url('/admin/categories/' . $c['id'] . '/supprimer') ?>" method="post" class="d-inline"
                      data-confirm="Supprimer la catégorie « <?= e($c['name']) ?> » ? Ses articles seront conservés sans catégorie.">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$categories): ?>
        <tr><td colspan="4" class="text-muted">Aucune catégorie.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
