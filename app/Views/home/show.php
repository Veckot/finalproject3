<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= site_url('/') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to all movies</a>
</div>

<article class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-[280px_1fr] gap-0">
        <!-- Poster -->
        <div class="bg-slate-900 aspect-[2/3] md:aspect-auto">
            <?php if (!empty($movie->pic)): ?>
                <img src="<?= esc($movie->pic) ?>"
                     alt="<?= esc($movie->name) ?>"
                     class="w-full h-full object-cover">
            <?php else: ?>
                <div class="flex items-center justify-center h-full text-slate-500 text-sm">
                    No poster
                </div>
            <?php endif; ?>
        </div>

        <!-- Details -->
        <div class="p-6 md:p-8">
            <div class="flex items-start justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold leading-tight">
                        <?= esc($movie->name) ?>
                        <?php if (!empty($movie->release_date)): ?>
                            <span class="text-slate-400 font-medium text-2xl">
                                (<?= esc(substr($movie->release_date, 0, 4)) ?>)
                            </span>
                        <?php endif; ?>
                    </h1>
                    <?php if (!empty($movie->original_title) && $movie->original_title !== $movie->name): ?>
                        <p class="text-sm text-slate-400 italic mt-1">
                            Original title: <?= esc($movie->original_title) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($movie->rating)): ?>
                    <div class="bg-amber-500 text-slate-900 px-3 py-2 rounded-lg text-center">
                        <div class="text-2xl font-bold leading-none">
                            &#9733; <?= number_format((float) $movie->rating, 1) ?>
                        </div>
                        <div class="text-[10px] uppercase tracking-wide font-semibold mt-0.5">
                            <?= esc($movie->vote_count ?? 0) ?> votes
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Genres -->
            <?php if (!empty($genres)): ?>
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach ($genres as $g): ?>
                        <span class="px-3 py-1 rounded-full bg-slate-700 text-xs font-medium">
                            <?= esc($g->name) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Quick facts -->
            <dl class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <?php if (!empty($movie->status)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Status</dt>
                        <dd class="font-medium"><?= esc($movie->status) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->release_date)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Released</dt>
                        <dd class="font-medium"><?= esc($movie->release_date) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->runtime)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Runtime</dt>
                        <dd class="font-medium">
                            <?= esc($movie->runtime) ?> min
                            <span class="text-slate-500 text-xs">
                                (<?= floor($movie->runtime / 60) ?>h <?= $movie->runtime % 60 ?>m)
                            </span>
                        </dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->original_language)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Language</dt>
                        <dd class="font-medium uppercase"><?= esc($movie->original_language) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->budget)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Budget</dt>
                        <dd class="font-medium">$<?= number_format((float) $movie->budget) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->revenue)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Revenue</dt>
                        <dd class="font-medium">$<?= number_format((float) $movie->revenue) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->popularity)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Popularity</dt>
                        <dd class="font-medium"><?= number_format((float) $movie->popularity, 1) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($movie->adult)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Rated</dt>
                        <dd class="font-medium text-rose-400">Adult</dd>
                    </div>
                <?php endif; ?>
            </dl>

            <!-- Overview -->
            <?php if (!empty($movie->description)): ?>
                <div class="mt-6">
                    <h2 class="text-lg font-semibold mb-2">Overview</h2>
                    <p class="text-slate-300 leading-relaxed"><?= esc($movie->description) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<!-- People -->
<section class="mt-10">
    <h2 class="text-2xl font-bold mb-4">People Involved</h2>

    <?php if (empty($actors) && empty($directors)): ?>
        <p class="text-slate-400">No credited people for this movie.</p>
    <?php endif; ?>

    <?php if (!empty($directors)): ?>
        <h3 class="text-sm uppercase tracking-wide text-slate-400 font-semibold mt-4 mb-3">
            Director<?= count($directors) > 1 ? 's' : '' ?>
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <?php foreach ($directors as $p): ?>
                <?= view('home/_person_card', ['p' => $p]) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($actors)): ?>
        <h3 class="text-sm uppercase tracking-wide text-slate-400 font-semibold mt-4 mb-3">
            Top Cast
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach ($actors as $p): ?>
                <?= view('home/_person_card', ['p' => $p]) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
