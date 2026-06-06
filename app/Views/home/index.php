<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$input_class = 'px-3 py-2 rounded-md bg-slate-800 border border-slate-700 focus:outline-none focus:border-indigo-500 text-sm';
$has_filters = ($q ?? '') !== '' || !empty($genre_id) || ($year ?? '') !== '' || ($rating ?? '') !== '';
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold">Popular Movies</h1>
    <p class="text-slate-400 mt-1">Browse the catalogue &mdash; <?= esc($total) ?> titles</p>
</div>

<form method="get" action="<?= site_url('/') ?>" class="mb-8 flex flex-wrap items-end gap-2">
    <input type="text" name="q" value="<?= esc($q ?? '', 'attr') ?>"
           placeholder="Search title..." class="<?= $input_class ?> flex-1 min-w-[10rem]">

    <select name="genre" class="<?= $input_class ?>">
        <option value="">All genres</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?= $g->id ?>" <?= (int) $genre_id === (int) $g->id ? 'selected' : '' ?>>
                <?= esc($g->name) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="year" value="<?= esc($year ?? '', 'attr') ?>"
           placeholder="Year" min="1870" max="2100" class="<?= $input_class ?> w-24">

    <select name="rating" class="<?= $input_class ?>">
        <option value="">Any rating</option>
        <?php foreach ([9, 8, 7, 6, 5] as $r): ?>
            <option value="<?= $r ?>" <?= (string) ($rating ?? '') === (string) $r ? 'selected' : '' ?>>
                &#9733; <?= $r ?>+
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 text-sm font-medium">
        Filter
    </button>
    <?php if ($has_filters): ?>
        <a href="<?= site_url('/') ?>" class="px-4 py-2 rounded-md border border-slate-600 hover:bg-slate-700 text-sm">
            Clear
        </a>
    <?php endif; ?>
</form>

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
