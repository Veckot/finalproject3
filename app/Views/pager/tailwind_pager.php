<?php
/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<nav aria-label="Pagination" class="inline-flex items-center gap-1">
    <?php if ($pager->hasPrevious()): ?>
        <a href="<?= $pager->getPrevious() ?>"
           class="px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm">
            &laquo; Prev
        </a>
    <?php else: ?>
        <span class="px-3 py-2 rounded-md bg-slate-800/50 border border-slate-800 text-slate-500 text-sm cursor-not-allowed">
            &laquo; Prev
        </span>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
        <?php if ($link['active']): ?>
            <span class="px-3 py-2 rounded-md bg-indigo-500 text-white text-sm font-semibold">
                <?= $link['title'] ?>
            </span>
        <?php else: ?>
            <a href="<?= $link['uri'] ?>"
               class="px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm">
                <?= $link['title'] ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()): ?>
        <a href="<?= $pager->getNext() ?>"
           class="px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm">
            Next &raquo;
        </a>
    <?php else: ?>
        <span class="px-3 py-2 rounded-md bg-slate-800/50 border border-slate-800 text-slate-500 text-sm cursor-not-allowed">
            Next &raquo;
        </span>
    <?php endif; ?>
</nav>
