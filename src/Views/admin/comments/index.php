<h1 class="h3 mb-3">Commentaires</h1>

<ul class="nav nav-pills mb-3">
    <?php
    $tabs = ['' => 'Tous', 'pending' => 'En attente', 'approved' => 'Approuvés', 'rejected' => 'Rejetés'];
    foreach ($tabs as $key => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?= $filter === $key ? 'active' : '' ?>" href="<?= url('/admin/commentaires') . ($key ? '?statut=' . $key : '') ?>"><?= e($label) ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if (!$comments): ?>
    <p class="text-muted">Aucun commentaire dans cette liste.</p>
<?php endif; ?>

<?php foreach ($comments as $c): ?>
    <div class="card mb-2">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="small text-muted">
                    <strong><?= e($c['pseudo']) ?></strong> sur
                    <a href="<?= url('/article/' . $c['article_slug']) ?>"><?= e($c['article_title']) ?></a>,
                    le <?= e(date_fr($c['created_at'])) ?>
                </div>
                <div>
                    <?php if ($c['status'] === 'pending'): ?><span class="badge text-bg-warning">En attente</span>
                    <?php elseif ($c['status'] === 'approved'): ?><span class="badge text-bg-success">Approuvé</span>
                    <?php else: ?><span class="badge text-bg-secondary">Rejeté</span><?php endif; ?>
                </div>
            </div>
            <p class="my-2"><?= nl2br(e($c['content'])) ?></p>
            <div class="d-flex gap-2">
                <?php if ($c['status'] !== 'approved'): ?>
                    <form action="<?= url('/admin/commentaires/' . $c['id'] . '/approuver') ?>" method="post">
                        <?= csrf_field() ?><button class="btn btn-sm btn-success">Approuver</button>
                    </form>
                <?php endif; ?>
                <?php if ($c['status'] !== 'rejected'): ?>
                    <form action="<?= url('/admin/commentaires/' . $c['id'] . '/rejeter') ?>" method="post">
                        <?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary">Rejeter</button>
                    </form>
                <?php endif; ?>
                <form action="<?= url('/admin/commentaires/' . $c['id'] . '/supprimer') ?>" method="post" data-confirm="Supprimer ce commentaire définitivement ?">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
