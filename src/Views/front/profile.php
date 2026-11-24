<div class="narrow">
    <h1>Mon profil</h1>
    <p class="muted">Compte créé le <?= e(date_fr($user['created_at'])) ?>. Rôle : <?= e(role_label($user['role'])) ?>.</p>

    <h2>Mes informations</h2>
    <form action="<?= url('/profil') ?>" method="post" class="form" novalidate>
        <?= csrf_field() ?>
        <label for="pseudo">Pseudo</label>
        <input type="text" id="pseudo" name="pseudo" value="<?= isset($_POST['pseudo']) ? old('pseudo') : e($user['pseudo']) ?>" required>
        <?php if (isset($profileErrors['pseudo'])): ?><p class="field-error"><?= e($profileErrors['pseudo']) ?></p><?php endif; ?>

        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? old('email') : e($user['email']) ?>" required>
        <?php if (isset($profileErrors['email'])): ?><p class="field-error"><?= e($profileErrors['email']) ?></p><?php endif; ?>

        <button type="submit">Enregistrer</button>
    </form>

    <h2>Changer de mot de passe</h2>
    <form action="<?= url('/profil/mot-de-passe') ?>" method="post" class="form" novalidate>
        <?= csrf_field() ?>
        <label for="current_password">Mot de passe actuel</label>
        <input type="password" id="current_password" name="current_password" required>
        <?php if (isset($passwordErrors['current_password'])): ?><p class="field-error"><?= e($passwordErrors['current_password']) ?></p><?php endif; ?>

        <label for="password">Nouveau mot de passe</label>
        <input type="password" id="password" name="password" required>
        <?php if (isset($passwordErrors['password'])): ?><p class="field-error"><?= e($passwordErrors['password']) ?></p><?php endif; ?>

        <label for="password_confirm">Confirmer le nouveau mot de passe</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
        <?php if (isset($passwordErrors['password_confirm'])): ?><p class="field-error"><?= e($passwordErrors['password_confirm']) ?></p><?php endif; ?>

        <button type="submit">Modifier le mot de passe</button>
    </form>
</div>
