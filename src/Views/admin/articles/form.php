<?php
$isEdit = $article !== null;
$action = $isEdit ? url('/admin/articles/' . $article['id'] . '/modifier') : url('/admin/articles/creer');
// Apres une erreur on reaffiche ce qui a ete saisi, sinon les valeurs de l'article
$val = function (string $field, string $default = '') use ($article) {
    if (isset($_POST[$field])) {
        return e($_POST[$field]);
    }
    return e($article[$field] ?? $default);
};
$currentCat = isset($_POST['category_id']) ? (int) $_POST['category_id'] : (int) ($article['category_id'] ?? 0);
$currentStatus = $_POST['status'] ?? ($article['status'] ?? 'draft');
?>
<h1 class="h3 mb-4"><?= e($title) ?></h1>

<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="card card-body" novalidate>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="title" class="form-label">Titre</label>
        <input type="text" id="title" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= $val('title') ?>" required>
        <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= e($errors['title']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="slug" class="form-label">Slug (adresse de l'article)</label>
        <input type="text" id="slug" name="slug" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" value="<?= $val('slug') ?>">
        <div class="form-text">Laissez vide pour le générer à partir du titre.</div>
        <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?= e($errors['slug']) ?></div><?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="category_id" class="form-label">Catégorie</label>
            <select id="category_id" name="category_id" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>">
                <option value="0">Aucune</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= $currentCat === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category_id'])): ?><div class="invalid-feedback"><?= e($errors['category_id']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Statut</label>
            <select id="status" name="status" class="form-select">
                <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>Publié</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label for="content" class="form-label">Contenu</label>
        <div class="btn-group btn-group-sm mb-2" role="group" aria-label="Mise en forme">
            <button type="button" class="btn btn-outline-secondary" data-insert="## ">Sous-titre</button>
            <button type="button" class="btn btn-outline-secondary" data-insert="- ">Liste</button>
        </div>
        <textarea id="content" name="content" rows="14" class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" required><?= $val('content') ?></textarea>
        <div class="form-text">Séparez les paragraphes par une ligne vide. Une ligne commençant par « ## » devient un sous-titre, une ligne commençant par « - » un élément de liste.</div>
        <?php if (isset($errors['content'])): ?><div class="invalid-feedback"><?= e($errors['content']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="cover" class="form-label">Image de couverture</label>
        <?php if ($isEdit && $article['cover_image']): ?>
            <div class="mb-2">
                <img src="<?= asset('uploads/' . $article['cover_image']) ?>" alt="" class="img-thumbnail" style="max-height: 140px;">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="remove_cover" name="remove_cover" value="1">
                    <label class="form-check-label" for="remove_cover">Supprimer l'image actuelle</label>
                </div>
            </div>
        <?php endif; ?>
        <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" class="form-control <?= isset($errors['cover']) ? 'is-invalid' : '' ?>">
        <div class="form-text">JPEG, PNG ou WebP, 2 Mo maximum.</div>
        <?php if (isset($errors['cover'])): ?><div class="invalid-feedback"><?= e($errors['cover']) ?></div><?php endif; ?>
        <img id="cover-preview" alt="" class="img-thumbnail mt-2 d-none" style="max-height: 140px;">
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= url('/admin/articles') ?>" class="btn btn-link">Annuler</a>
    </div>
</form>
