<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-8 flex items-end justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-bold"><?= esc($genre->name) ?></h1>
        <p class="text-slate-400 mt-1">Movies in this genre</p>
    </div>
    <a href="<?= site_url('genres') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; All genres</a>
</div>

<?php if (empty($movies)): ?>
    <div class="text-center py-20 text-slate-400">No movies in this genre yet.</div>
<?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <?php foreach ($movies as $movie): ?>
            <?= view('home/_movie_card', ['movie' => $movie]) ?>
        <?php endforeach; ?>
    </div>

    <div class="mt-10 flex justify-center">
        <?= $pager->links('default', 'movies_pager') ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
