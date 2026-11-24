<h1 class="h3 mb-3">Utilisateurs</h1>

<div class="table-responsive">
<table class="table table-striped align-middle bg-white">
    <thead><tr><th>Pseudo</th><th>E-mail</th><th>Rôle</th><th>Inscrit le</th><th>État</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <?php $isMe = (int) $u['id'] === (int) current_user()['id']; ?>
        <tr>
            <td><?= e($u['pseudo']) ?><?= $isMe ? ' (vous)' : '' ?></td>
            <td><?= e($u['email']) ?></td>
            <td>
                <?php if ($isMe): ?>
                    <?= e(role_label($u['role'])) ?>
                <?php else: ?>
                    <form action="<?= url('/admin/utilisateurs/' . $u['id'] . '/role') ?>" method="post" class="d-flex gap-1">
                        <?= csrf_field() ?>
                        <select name="role" class="form-select form-select-sm" aria-label="Rôle de <?= e($u['pseudo']) ?>">
                            <?php foreach (['USER', 'AUTHOR', 'ADMIN'] as $r): ?>
                                <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= e(role_label($r)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-primary">OK</button>
                    </form>
                <?php endif; ?>
            </td>
            <td><?= e(date_fr($u['created_at'])) ?></td>
            <td><?= (int) $u['is_banned'] === 1 ? '<span class="badge text-bg-danger">Banni</span>' : '<span class="badge text-bg-success">Actif</span>' ?></td>
            <td class="text-end text-nowrap">
                <?php if (!$isMe): ?>
                    <form action="<?= url('/admin/utilisateurs/' . $u['id'] . '/bannir') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-warning"><?= (int) $u['is_banned'] === 1 ? 'Réactiver' : 'Bannir' ?></button>
                    </form>
                    <form action="<?= url('/admin/utilisateurs/' . $u['id'] . '/supprimer') ?>" method="post" class="d-inline"
                          data-confirm="Supprimer le compte de <?= e($u['pseudo']) ?> ? Ses articles et commentaires seront supprimés aussi.">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
