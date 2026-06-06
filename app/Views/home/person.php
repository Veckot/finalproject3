<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// Resolve the profile image (TMDB path or full URL).
$img = !empty($person->profile_path)
    ? (str_starts_with($person->profile_path, 'http')
        ? $person->profile_path
        : 'https://image.tmdb.org/t/p/w300' . $person->profile_path)
    : null;

// Human-readable gender.
$genders = [1 => 'Female', 2 => 'Male', 3 => 'Non-binary'];
$gender  = $genders[(int) $person->gender] ?? null;

// Age (at death if deceased, otherwise today).
$age = null;
if (!empty($person->birthday)) {
    try {
        $from = new DateTime($person->birthday);
        $to   = !empty($person->deathday) ? new DateTime($person->deathday) : new DateTime('today');
        $age  = $from->diff($to)->y;
    } catch (Exception $e) {
        $age = null;
    }
}
?>

<div class="mb-4">
    <a href="<?= site_url('/') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to all movies</a>
</div>

<article class="bg-slate-800 rounded-xl border border-slate-700 p-6 md:p-8">
    <div class="flex flex-col sm:flex-row gap-6">
        <!-- Photo (fixed small size, left on desktop, on top for mobile) -->
        <div class="w-40 sm:w-48 shrink-0 mx-auto sm:mx-0">
            <div class="aspect-[2/3] rounded-lg overflow-hidden bg-slate-700">
                <?php if ($img): ?>
                    <img src="<?= esc($img) ?>" alt="<?= esc($person->name) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="flex items-center justify-center h-full text-slate-500 text-xs">No photo</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bio -->
        <div class="flex-1 min-w-0">
            <h1 class="text-3xl md:text-4xl font-bold leading-tight"><?= esc($person->name) ?></h1>
            <?php if (!empty($person->known_for_department)): ?>
                <p class="text-slate-400 mt-1">Known for <?= esc($person->known_for_department) ?></p>
            <?php endif; ?>

            <dl class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <?php if ($gender): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Gender</dt>
                        <dd class="font-medium"><?= esc($gender) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($person->birthday)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Born</dt>
                        <dd class="font-medium">
                            <?= esc($person->birthday) ?>
                            <?php if ($age !== null && empty($person->deathday)): ?>
                                <span class="text-slate-500 text-xs">(age <?= $age ?>)</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($person->deathday)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Died</dt>
                        <dd class="font-medium">
                            <?= esc($person->deathday) ?>
                            <?php if ($age !== null): ?>
                                <span class="text-slate-500 text-xs">(aged <?= $age ?>)</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($person->place_of_birth)): ?>
                    <div class="col-span-2">
                        <dt class="text-slate-400 text-xs uppercase">Place of birth</dt>
                        <dd class="font-medium"><?= esc($person->place_of_birth) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($person->popularity)): ?>
                    <div>
                        <dt class="text-slate-400 text-xs uppercase">Popularity</dt>
                        <dd class="font-medium"><?= number_format((float) $person->popularity, 1) ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</article>

<!-- Filmography -->
<section class="mt-10">
    <h2 class="text-2xl font-bold mb-4">
        Filmography
        <span class="text-slate-500 text-lg font-normal">(<?= count($movies) ?>)</span>
    </h2>

    <?php if (empty($movies)): ?>
        <p class="text-slate-400">No movies linked to this person yet.</p>
    <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach ($movies as $m): ?>
                <a href="<?= site_url('movie/' . $m->id) ?>"
                   class="block bg-slate-800 rounded-lg overflow-hidden border border-slate-700 hover:border-indigo-500 hover:-translate-y-0.5 transition">
                    <div class="aspect-[2/3] bg-slate-700 relative">
                        <?php if (!empty($m->pic)): ?>
                            <img src="<?= esc($m->pic) ?>" alt="<?= esc($m->name) ?>"
                                 class="w-full h-full object-cover" loading="lazy">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full text-slate-500 text-xs">No poster</div>
                        <?php endif; ?>
                        <span class="absolute bottom-1 left-1 px-2 py-0.5 rounded bg-slate-900/80 text-[10px] capitalize">
                            <?= esc($m->role) ?>
                        </span>
                    </div>
                    <div class="p-2.5">
                        <p class="text-sm font-semibold leading-tight truncate" title="<?= esc($m->name) ?>">
                            <?= esc($m->name) ?>
                        </p>
                        <p class="text-xs text-slate-400">
                            <?= !empty($m->release_date) ? esc(substr($m->release_date, 0, 4)) : '—' ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
