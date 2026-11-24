<article class="article-row">
    <a class="row-cover" href="<?= url('/article/' . $a['slug']) ?>" tabindex="-1" aria-hidden="true">
        <?php if ($a['cover_image']): ?>
            <img src="<?= asset('uploads/' . $a['cover_image']) ?>" alt="">
        <?php else: ?>
            <span class="no-cover"><?= e(mb_strtoupper(mb_substr($a['title'], 0, 1))) ?></span>
        <?php endif; ?>
    </a>
    <div class="row-text">
        <h2><a href="<?= url('/article/' . $a['slug']) ?>"><?= e($a['title']) ?></a></h2>
        <p class="meta">
            Publié le <?= e(date_fr($a['published_at'])) ?> par <?= e($a['author']) ?>
            <?php if ($a['category_name']): ?>
                dans <a href="<?= url('/categorie/' . $a['category_slug']) ?>"><?= e($a['category_name']) ?></a>
            <?php endif; ?>
        </p>
        <p><?= e(excerpt($a['content'])) ?></p>
    </div>
</article>
