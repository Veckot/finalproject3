<?php
// Auth state for the navbar (recomputed each request).
$ion_auth  = new \IonAuth\Libraries\IonAuth();
$logged_in = $ion_auth->loggedIn();
$is_admin  = $logged_in && $ion_auth->isAdmin();
$user      = $logged_in ? $ion_auth->user()->row() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'CineDB') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">

<nav class="bg-slate-800/80 backdrop-blur sticky top-0 z-10 border-b border-slate-700">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
        <a href="<?= site_url('/') ?>" class="flex items-center gap-2 text-xl font-bold">
            <span class="text-amber-400">&#9733;</span>
            <span>CineDB</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="<?= site_url('/') ?>" class="px-3 py-1.5 rounded-md hover:bg-slate-700 transition text-sm">
                Home
            </a>
            <a href="<?= site_url('genres') ?>" class="px-3 py-1.5 rounded-md hover:bg-slate-700 transition text-sm">
                Genres
            </a>
            <a href="<?= site_url('people') ?>" class="px-3 py-1.5 rounded-md hover:bg-slate-700 transition text-sm">
                People
            </a>
            <a href="<?= site_url('stats') ?>" class="px-3 py-1.5 rounded-md hover:bg-slate-700 transition text-sm">
                Stats
            </a>

            <?php if ($logged_in): ?>
                <span class="text-sm text-slate-400 hidden sm:inline">
                    Hi, <?= esc($user->username ?? $user->email) ?>
                </span>

                <?php if ($is_admin): ?>
                    <a href="<?= site_url('admin') ?>"
                       class="px-4 py-1.5 rounded-md bg-amber-500 text-slate-900 font-semibold hover:bg-amber-400 transition text-sm">
                        Admin Panel
                    </a>
                <?php endif; ?>

                <a href="<?= site_url('auth/logout') ?>"
                   class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 transition text-sm">
                    Logout
                </a>
            <?php else: ?>
                <a href="<?= site_url('auth/register') ?>"
                   class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 transition text-sm">
                    Register
                </a>
                <a href="<?= site_url('auth/login') ?>"
                   class="px-4 py-1.5 rounded-md bg-indigo-500 hover:bg-indigo-400 transition text-sm font-medium">
                    Log in
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-6 py-8">
    <?= view('partials/_alerts') ?>
    <?php if (! empty($crumbs)): ?>
        <?= view('partials/_breadcrumbs', ['crumbs' => $crumbs]) ?>
    <?php endif; ?>
    <?= $this->renderSection('content') ?>
</main>

<footer class="border-t border-slate-700 mt-12 py-6 text-center text-sm text-slate-500">
    CineDB &middot; built with CodeIgniter 4 + Tailwind
</footer>

<script src="<?= base_url('assets/js/app.js') ?>" defer></script>
</body>
</html>
