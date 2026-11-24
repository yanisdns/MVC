<h1 class="h3 mb-4"><?= e($title) ?></h1>
<form action="<?= url('/admin/categories/' . $category['id'] . '/modifier') ?>" method="post" class="card card-body" style="max-width: 480px;" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Nom</label>
        <input type="text" id="name" name="name" class="form-control <?= $error ? 'is-invalid' : '' ?>"
               value="<?= isset($_POST['name']) ? old('name') : e($category['name']) ?>" required>
        <?php if ($error): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= url('/admin/categories') ?>" class="btn btn-link">Annuler</a>
    </div>
</form>
