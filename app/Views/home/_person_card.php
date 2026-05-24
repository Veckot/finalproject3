<?php
/** @var object $p */
$img = !empty($p->profile_path)
    ? (str_starts_with($p->profile_path, 'http')
        ? $p->profile_path
        : 'https://image.tmdb.org/t/p/w185' . $p->profile_path)
    : null;
?>
<div class="bg-slate-800 rounded-lg overflow-hidden border border-slate-700">
    <div class="aspect-[3/4] bg-slate-700">
        <?php if ($img): ?>
            <img src="<?= esc($img) ?>" alt="<?= esc($p->name) ?>"
                 class="w-full h-full object-cover" loading="lazy">
        <?php else: ?>
            <div class="flex items-center justify-center h-full text-slate-500 text-xs">No photo</div>
        <?php endif; ?>
    </div>
    <div class="p-2.5">
        <p class="text-sm font-semibold leading-tight truncate" title="<?= esc($p->name) ?>">
            <?= esc($p->name) ?>
        </p>
        <p class="text-xs text-slate-400 capitalize">
            <?= esc($p->role ?? $p->known_for_department ?? '') ?>
        </p>
    </div>
</div>
