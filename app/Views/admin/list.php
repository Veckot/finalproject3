<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold"><?= esc($title) ?></h1>
        <p class="text-slate-400 mt-1">Edit or delete entries below.</p>
    </div>
    <a href="<?= site_url('admin') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to panel</a>
</div>

<div class="mb-4 flex gap-2 flex-wrap">
    <?php foreach (['movie' => 'Movies', 'genre' => 'Genres', 'person' => 'People'] as $val => $label):
        $isActive = $entity === $val; ?>
        <a href="<?= site_url('admin/list?entity=' . $val) ?>"
           class="px-3 py-1.5 rounded-md text-sm <?= $isActive ? 'bg-indigo-500 text-white' : 'bg-slate-800 border border-slate-700 hover:bg-slate-700' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900 text-slate-400 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">Name</th>
                <?php if ($entity === 'movie'): ?>
                    <th class="px-4 py-3 text-left">Year</th>
                    <th class="px-4 py-3 text-left">Rating</th>
                <?php elseif ($entity === 'person'): ?>
                    <th class="px-4 py-3 text-left">Dept</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr class="border-t border-slate-700 hover:bg-slate-700/30">
                <td class="px-4 py-3 text-slate-400"><?= esc($item->id) ?></td>
                <td class="px-4 py-3 font-medium"><?= esc($item->name) ?></td>
                <?php if ($entity === 'movie'): ?>
                    <td class="px-4 py-3 text-slate-400"><?= !empty($item->release_date) ? esc(substr($item->release_date, 0, 4)) : '—' ?></td>
                    <td class="px-4 py-3 text-amber-400"><?= !empty($item->rating) ? esc($item->rating) : '—' ?></td>
                <?php elseif ($entity === 'person'): ?>
                    <td class="px-4 py-3 text-slate-400"><?= esc($item->known_for_department ?? '—') ?></td>
                <?php endif; ?>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="<?= site_url('admin/edit/' . $entity . '/' . $item->id) ?>"
                       class="inline-block px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-500 text-xs font-medium">
                        Edit
                    </a>
                    <?= form_open(site_url('admin/delete/' . $entity . '/' . $item->id), [
                        'class'   => 'inline',
                        'onsubmit' => "return confirm('Delete this " . esc($entity) . "?');",
                    ]) ?>
                        <button type="submit" class="px-3 py-1 rounded bg-rose-600 hover:bg-rose-500 text-xs font-medium">
                            Delete
                        </button>
                    <?= form_close() ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-slate-500">No entries.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    <?= $pager->links('default', 'movies_pager') ?>
</div>

<?= $this->endSection() ?>
