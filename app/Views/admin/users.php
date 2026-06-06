<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold">Manage Users</h1>
        <p class="text-slate-400 mt-1">Edit accounts, roles and access.</p>
    </div>
    <a href="<?= site_url('admin') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to panel</a>
</div>

<form method="get" action="<?= site_url('admin/users') ?>" class="mb-4 flex gap-2">
    <input type="text" name="q" value="<?= esc($q ?? '', 'attr') ?>"
           placeholder="Search username or email..."
           class="flex-1 max-w-sm px-4 py-2 rounded-md bg-slate-800 border border-slate-700 focus:outline-none focus:border-indigo-500 text-sm">
    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 text-sm font-medium">Search</button>
    <?php if (!empty($q)): ?>
        <a href="<?= site_url('admin/users') ?>" class="px-4 py-2 rounded-md border border-slate-600 hover:bg-slate-700 text-sm">Clear</a>
    <?php endif; ?>
</form>

<div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900 text-slate-400 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">Username</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $u): ?>
            <tr class="border-t border-slate-700 hover:bg-slate-700/30">
                <td class="px-4 py-3 text-slate-400"><?= esc($u->id) ?></td>
                <td class="px-4 py-3 font-medium">
                    <?= esc($u->username) ?>
                    <?php if (!empty($u->is_admin)): ?>
                        <span class="ml-1 px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs">admin</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-slate-300"><?= esc($u->email) ?></td>
                <td class="px-4 py-3">
                    <?php if (!empty($u->active)): ?>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs">active</span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full bg-slate-600/40 text-slate-300 text-xs">inactive</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="<?= site_url('admin/users/' . $u->id . '/edit') ?>"
                       class="inline-block px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-500 text-xs font-medium">Edit</a>
                    <?= form_open(site_url('admin/users/' . $u->id . '/delete'), [
                        'class'    => 'inline',
                        'onsubmit' => "return confirm('Delete user " . esc($u->username, 'js') . "?');",
                    ]) ?>
                        <button type="submit" class="px-3 py-1 rounded bg-rose-600 hover:bg-rose-500 text-xs font-medium">Delete</button>
                    <?= form_close() ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
            <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">
                <?= !empty($q) ? 'No users match "' . esc($q) . '".' : 'No users.' ?>
            </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    <?= $pager->links('default', 'movies_pager') ?>
</div>

<?= $this->endSection() ?>
