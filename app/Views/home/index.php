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
            <a href="<?= site_url('movie/' . $movie->id) ?>"
               class="bg-slate-800 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-200 flex flex-col">
                <div class="aspect-[2/3] bg-slate-700 relative overflow-hidden">
                    <?php if (!empty($movie->pic)): ?>
                        <img src="<?= esc($movie->pic) ?>"
                             alt="<?= esc($movie->name) ?>"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-slate-500 text-sm">
                            No poster
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($movie->rating)): ?>
                        <span class="absolute top-2 right-2 bg-amber-500 text-slate-900 text-xs font-bold px-2 py-1 rounded">
                            &#9733; <?= number_format((float) $movie->rating, 1) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="p-3 flex-1 flex flex-col">
                    <h3 class="font-semibold text-sm leading-tight mb-1" title="<?= esc($movie->name) ?>">
                        <?= esc($movie->name) ?>
                    </h3>
                    <p class="text-xs text-slate-400 mb-2">
                        <?= !empty($movie->release_date) ? esc(substr($movie->release_date, 0, 4)) : '&mdash;' ?>
                    </p>
                    <p class="text-xs text-slate-300 line-clamp-3 flex-1">
                        <?= esc($movie->description ?? '') ?>
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
