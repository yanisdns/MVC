<div class="layout">
    <section class="articles">
        <?php if ($category): ?>
            <h1>Catégorie : <?= e($category['name']) ?></h1>
        <?php elseif ($search !== ''): ?>
            <h1>Résultats pour « <?= e($search) ?> »</h1>
            <p class="muted"><?= (int) $total ?> article<?= $total > 1 ? 's' : '' ?> trouvé<?= $total > 1 ? 's' : '' ?>.</p>
        <?php else: ?>
            <h1>Derniers articles</h1>
        <?php endif; ?>

        <?php if (!$articles): ?>
            <p class="empty">Aucun article ne correspond à cette recherche. Essayez un autre mot ou parcourez les catégories.</p>
        <?php endif; ?>

        <?php foreach ($articles as $a): ?>
            <?php require VIEWS . '/front/partials_article_row.php'; ?>
        <?php endforeach; ?>

        <?php if ($pages > 1): ?>
            <?php
            $query = $search !== '' ? '&q=' . urlencode($search) : '';
            $baseUrl = $category ? url('/categorie/' . $category['slug']) : url('/');
            ?>
            <nav class="pagination" aria-label="Pagination">
                <?php if ($page > 1): ?>
                    <a href="<?= $baseUrl ?>?page=<?= $page - 1 . $query ?>">Précédent</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>?page=<?= $i . $query ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $pages): ?>
                    <a href="<?= $baseUrl ?>?page=<?= $page + 1 . $query ?>">Suivant</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </section>

    <aside class="sidebar">
        <form action="<?= url('/') ?>" method="get" class="search-form" role="search">
            <label for="q">Rechercher</label>
            <div class="search-line">
                <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Un mot du titre ou du texte">
                <button type="submit">OK</button>
            </div>
        </form>

        <h2>Catégories</h2>
        <ul class="cat-list">
            <li><a href="<?= url('/') ?>"<?= !$category ? ' class="active"' : '' ?>>Tous les articles</a></li>
            <?php foreach ($categories as $c): ?>
                <li>
                    <a href="<?= url('/categorie/' . $c['slug']) ?>"<?= ($category && $category['id'] == $c['id']) ? ' class="active"' : '' ?>>
                        <?= e($c['name']) ?>
                    </a>
                    <span class="count"><?= (int) $c['nb_articles'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
</div>
