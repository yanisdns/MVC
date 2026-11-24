<article class="single">
    <header>
        <h1><?= e($article['title']) ?></h1>
        <p class="meta">
            Publié le <?= e(date_fr($article['published_at'])) ?> par <?= e($article['author']) ?>
            <?php if ($article['category_name']): ?>
                dans <a href="<?= url('/categorie/' . $article['category_slug']) ?>"><?= e($article['category_name']) ?></a>
            <?php endif; ?>
        </p>
    </header>

    <?php if ($article['cover_image']): ?>
        <img class="single-cover" src="<?= asset('uploads/' . $article['cover_image']) ?>" alt="">
    <?php endif; ?>

    <div class="content">
        <?= format_content($article['content']) ?>
    </div>
</article>

<section class="comments" id="commentaires">
    <h2>Commentaires (<?= count($comments) ?>)</h2>

    <?php if (!$comments): ?>
        <p class="muted">Pas encore de commentaire. Vous pouvez ouvrir la discussion.</p>
    <?php endif; ?>

    <?php foreach ($comments as $c): ?>
        <div class="comment">
            <p class="comment-head"><strong><?= e($c['pseudo']) ?></strong>, le <?= e(date_fr($c['created_at'])) ?></p>
            <p><?= nl2br(e($c['content'])) ?></p>
        </div>
    <?php endforeach; ?>

    <?php if (current_user()): ?>
        <form action="<?= url('/article/' . $article['slug'] . '/commentaire') ?>" method="post" class="form comment-form">
            <?= csrf_field() ?>
            <label for="content">Votre commentaire</label>
            <textarea id="content" name="content" rows="4" maxlength="1000" required></textarea>
            <button type="submit">Publier</button>
        </form>
    <?php else: ?>
        <p class="muted"><a href="<?= url('/connexion') ?>">Connectez-vous</a> pour laisser un commentaire.</p>
    <?php endif; ?>
</section>
