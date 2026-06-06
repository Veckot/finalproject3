<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-8 flex items-end justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-bold">Popular Movies</h1>
        <p class="text-slate-400 mt-1">Browse the catalogue -; <?= esc($total) ?> titles</p>
    </div>

    <form method="get" action="<?= site_url('/') ?>" class="flex gap-2">
        <input type="text" name="q" value="<?= esc($q ?? '') ?>"
               placeholder="Search title..."
               class="px-4 py-2 rounded-md bg-slate-800 border border-slate-700 focus:outline-none focus:border-indigo-500 text-sm">
        <button type="submit"
                class="px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 text-sm font-medium">
            Search
        </button>
    </form>
</div>

<?php if (empty($movies)): ?>
    <div class="text-center py-20 text-slate-400">
        No movies found.
    </div>
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
