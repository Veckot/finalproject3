<?php
/**
 * Flash alert partial.
 *
 * Reads four flash keys from the session:
 *   success  – green  (redirects after add/update/delete/login)
 *   error    – red    (auth fail, not-found, unknown entity)
 *   warning  – amber  (soft cautions)
 *   info     – blue   (neutral information)
 *   errors   – red list (validation errors from the validator)
 *
 * Include in layout with:
 *   <?= view('partials/_alerts') ?>
 */

$alerts = [
    'success' => [
        'messages' => (array) session()->getFlashdata('success'),
        'icon'     => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        'base'     => 'bg-emerald-900/60 border-emerald-500 text-emerald-200',
        'btn'      => 'text-emerald-300 hover:text-emerald-100',
    ],
    'error' => [
        'messages' => (array) session()->getFlashdata('error'),
        'icon'     => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        'base'     => 'bg-rose-900/60 border-rose-500 text-rose-200',
        'btn'      => 'text-rose-300 hover:text-rose-100',
    ],
    'warning' => [
        'messages' => (array) session()->getFlashdata('warning'),
        'icon'     => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
        'base'     => 'bg-amber-900/60 border-amber-500 text-amber-200',
        'btn'      => 'text-amber-300 hover:text-amber-100',
    ],
    'info' => [
        'messages' => (array) session()->getFlashdata('info'),
        'icon'     => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
        'base'     => 'bg-sky-900/60 border-sky-500 text-sky-200',
        'btn'      => 'text-sky-300 hover:text-sky-100',
    ],
];

// Validation errors (list)
$validationErrors = (array) session()->getFlashdata('errors');
$validationErrors = array_filter($validationErrors);
?>

<div id="alert-stack" class="space-y-3 mb-6" role="alert" aria-live="polite">

    <?php foreach ($alerts as $type => $cfg):
        $messages = array_filter($cfg['messages']);
        if (empty($messages)) continue;
    ?>
        <?php foreach ($messages as $msg): ?>
            <div data-alert
                 class="flex items-start gap-3 px-4 py-3 rounded-lg border <?= $cfg['base'] ?> shadow-lg animate-fade-in">
                <?= $cfg['icon'] ?>
                <p class="flex-1 text-sm leading-relaxed"><?= esc($msg) ?></p>
                <button type="button"
                        onclick="this.closest('[data-alert]').remove()"
                        class="<?= $cfg['btn'] ?> text-lg leading-none mt-0.5 transition"
                        aria-label="Dismiss">
                    &times;
                </button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php if (!empty($validationErrors)): ?>
        <div data-alert
             class="flex items-start gap-3 px-4 py-3 rounded-lg border bg-rose-900/60 border-rose-500 text-rose-200 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm space-y-0.5">
                    <?php foreach ($validationErrors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button type="button"
                    onclick="this.closest('[data-alert]').remove()"
                    class="text-rose-300 hover:text-rose-100 text-lg leading-none mt-0.5 transition"
                    aria-label="Dismiss">
                &times;
            </button>
        </div>
    <?php endif; ?>

</div>

<script>
// Auto-dismiss success/info alerts after 5 s
document.addEventListener('DOMContentLoaded', function () {
    const autoDismiss = ['success', 'info'];
    document.querySelectorAll('[data-alert]').forEach(function (el) {
        // Only auto-dismiss if it doesn't contain a list (validation errors)
        if (el.querySelector('ul')) return;

        const isSoftAlert =
            el.classList.contains('bg-emerald-900/60') ||
            el.classList.contains('bg-sky-900/60');

        if (isSoftAlert) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity    = '0';
                setTimeout(function () { el.remove(); }, 500);
            }, 5000);
        }
    });
});
</script>
