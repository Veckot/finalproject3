<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-8 flex items-end justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-bold">People</h1>
        <p class="text-slate-400 mt-1">Browse cast &amp; crew &mdash; <?= esc($total) ?> people</p>
    </div>

    <form method="get" action="<?= site_url('people') ?>" class="flex gap-2">
        <input type="text" name="q" value="<?= esc($q ?? '', 'attr') ?>"
               placeholder="Search name..."
               class="px-4 py-2 rounded-md bg-slate-800 border border-slate-700 focus:outline-none focus:border-indigo-500 text-sm">
        <button type="submit" class="px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 text-sm font-medium">
            Search
        </button>
    </form>
</div>

<?php if (empty($people)): ?>
    <div class="text-center py-20 text-slate-400">No people found.</div>
<?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach ($people as $person): ?>
            <?php
            $img = !empty($person->profile_path)
                ? (str_starts_with($person->profile_path, 'http')
                    ? $person->profile_path
                    : 'https://image.tmdb.org/t/p/w185' . $person->profile_path)
                : null;
            ?>
            <a href="<?= site_url('person/' . $person->id) ?>"
               class="block bg-slate-800 rounded-lg overflow-hidden border border-slate-700 hover:border-indigo-500 hover:-translate-y-0.5 transition">
                <div class="aspect-[3/4] bg-slate-700">
                    <?php if ($img): ?>
                        <img src="<?= esc($img) ?>" alt="<?= esc($person->name) ?>"
                             class="w-full h-full object-cover" loading="lazy">
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-slate-500 text-xs">No photo</div>
                    <?php endif; ?>
                </div>
                <div class="p-2.5">
                    <p class="text-sm font-semibold leading-tight truncate" title="<?= esc($person->name) ?>">
                        <?= esc($person->name) ?>
                    </p>
                    <p class="text-xs text-slate-400 truncate">
                        <?= esc($person->known_for_department ?? '') ?>
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="mt-10 flex justify-center">
        <?= $pager->links('default', 'movies_pager') ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
