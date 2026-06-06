<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-8">
    <h1 class="text-3xl font-bold">Catalogue Statistics</h1>
    <p class="text-slate-400 mt-1">Aggregated figures across all movies and genres.</p>
</div>

<!-- Totals (catalogue-wide aggregation) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">Movies</p>
        <p class="text-3xl font-bold mt-1"><?= number_format((int) $totals->total_movies) ?></p>
    </div>
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">Avg rating</p>
        <p class="text-3xl font-bold mt-1 text-amber-400">
            <?= $totals->avg_rating !== null ? number_format((float) $totals->avg_rating, 2) : '—' ?>
        </p>
    </div>
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">Total revenue</p>
        <p class="text-2xl font-bold mt-1 text-emerald-400">
            <?= $totals->total_revenue !== null ? '$' . number_format((float) $totals->total_revenue) : '—' ?>
        </p>
    </div>
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">Newest year</p>
        <p class="text-3xl font-bold mt-1"><?= $totals->newest_year ?? '—' ?></p>
    </div>
</div>

<!-- Per-genre breakdown (JOIN + GROUP BY aggregation) -->
<h2 class="text-2xl font-bold mb-4">By genre</h2>

<?php if (empty($genre_stats)): ?>
    <p class="text-slate-400">No data yet. Seed the catalogue first.</p>
<?php else: ?>
    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-900 text-slate-400 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Genre</th>
                    <th class="px-4 py-3 text-left w-1/2">Movies</th>
                    <th class="px-4 py-3 text-right">Avg rating</th>
                    <th class="px-4 py-3 text-right">Total revenue</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($genre_stats as $row): ?>
                <?php $pct = $max_count > 0 ? round(((int) $row->movie_count / $max_count) * 100) : 0; ?>
                <tr class="border-t border-slate-700 hover:bg-slate-700/30">
                    <td class="px-4 py-3 font-medium"><?= esc($row->genre) ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 bg-slate-900 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-500 h-full" style="width: <?= $pct ?>%"></div>
                            </div>
                            <span class="text-slate-300 tabular-nums w-10 text-right"><?= (int) $row->movie_count ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right text-amber-400">
                        <?= $row->avg_rating !== null ? number_format((float) $row->avg_rating, 2) : '—' ?>
                    </td>
                    <td class="px-4 py-3 text-right text-emerald-400">
                        <?= $row->total_revenue !== null ? '$' . number_format((float) $row->total_revenue) : '—' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
