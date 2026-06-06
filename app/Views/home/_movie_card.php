<?php
/**
 * Movie poster card.
 * Expects $movie (object) with: id, name, pic, rating, release_date, description.
 */
?>
<a href="<?= site_url('movie/' . $movie->id) ?>"
   class="bg-slate-800 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-200 flex flex-col">
    <div class="aspect-[2/3] bg-slate-700 relative overflow-hidden">
        <?php if (!empty($movie->pic)): ?>
            <img src="<?= esc($movie->pic) ?>" alt="<?= esc($movie->name) ?>"
                 class="w-full h-full object-cover" loading="lazy">
        <?php else: ?>
            <div class="flex items-center justify-center h-full text-slate-500 text-sm">No poster</div>
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
            <?= !empty($movie->release_date) ? esc(substr($movie->release_date, 0, 4)) : '—' ?>
        </p>
        <p class="text-xs text-slate-300 line-clamp-3 flex-1">
            <?= esc($movie->description ?? '') ?>
        </p>
    </div>
</a>
