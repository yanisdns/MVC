<div class="narrow">
    <h1>Connexion</h1>
    <?php if ($error): ?>
        <p class="flash flash-error"><?= e($error) ?></p>
    <?php endif; ?>
    <form action="<?= url('/connexion') ?>" method="post" class="form">
        <?= csrf_field() ?>
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" value="<?= old('email') ?>" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
    <p class="muted">Pas encore de compte ? <a href="<?= url('/inscription') ?>">Inscrivez-vous</a>.</p>
</div>
