<?php
/**
 * Breadcrumb navigation partial.
 *
 * Expects $crumbs: a list of ['label' => string, 'url' => string|null].
 * The last item is rendered as the current page (no link).
 *
 * Usage from a controller:
 *   view('some/view', ['crumbs' => [
 *       ['label' => 'Home',   'url' => site_url('/')],
 *       ['label' => 'Movies', 'url' => site_url('/')],
 *       ['label' => $movie->name, 'url' => null],
 *   ]]);
 */
if (empty($crumbs) || ! is_array($crumbs)) {
    return;
}
$last = array_key_last($crumbs);
?>
<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-slate-400">
        <?php foreach ($crumbs as $i => $crumb): ?>
            <li class="flex items-center gap-1">
                <?php if ($i !== array_key_first($crumbs)): ?>
                    <span class="text-slate-600">&rsaquo;</span>
                <?php endif; ?>

                <?php if (! empty($crumb['url']) && $i !== $last): ?>
                    <a href="<?= esc($crumb['url'], 'attr') ?>"
                       class="hover:text-slate-200 transition">
                        <?= esc($crumb['label']) ?>
                    </a>
                <?php else: ?>
                    <span class="text-slate-200 font-medium" aria-current="page">
                        <?= esc($crumb['label']) ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
