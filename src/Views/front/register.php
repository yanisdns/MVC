<div class="narrow">
    <h1>Inscription</h1>
    <form action="<?= url('/inscription') ?>" method="post" class="form" novalidate>
        <?= csrf_field() ?>

        <label for="pseudo">Pseudo</label>
        <input type="text" id="pseudo" name="pseudo" value="<?= old('pseudo') ?>" required>
        <?php if (isset($errors['pseudo'])): ?><p class="field-error"><?= e($errors['pseudo']) ?></p><?php endif; ?>

        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
        <?php if (isset($errors['email'])): ?><p class="field-error"><?= e($errors['email']) ?></p><?php endif; ?>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
        <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>

        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
        <?php if (isset($errors['password_confirm'])): ?><p class="field-error"><?= e($errors['password_confirm']) ?></p><?php endif; ?>

        <button type="submit">Créer mon compte</button>
    </form>
    <p class="muted">Déjà inscrit ? <a href="<?= url('/connexion') ?>">Connectez-vous</a>.</p>
</div>
