<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-8">
    <h1 class="text-3xl font-bold">Genres</h1>
    <p class="text-slate-400 mt-1">Pick a genre to browse its films.</p>
</div>

<?php if (empty($genres)): ?>
    <div class="text-center py-20 text-slate-400">No genres found.</div>
<?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <?php foreach ($genres as $genre): ?>
            <a href="<?= site_url('genre/' . $genre->id) ?>"
               class="flex items-center justify-between gap-3 px-5 py-4 rounded-lg bg-slate-800 border border-slate-700 hover:border-indigo-500 hover:-translate-y-0.5 transition">
                <span class="font-semibold"><?= esc($genre->name) ?></span>
                <span class="shrink-0 px-2.5 py-1 rounded-full bg-slate-700 text-xs text-slate-300">
                    <?= (int) $genre->movie_count ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
