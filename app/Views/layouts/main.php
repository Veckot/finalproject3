<?php
$ionAuth   = new \IonAuth\Libraries\IonAuth();
$loggedIn  = $ionAuth->loggedIn();
$isAdmin   = $loggedIn && $ionAuth->isAdmin();
$user      = $loggedIn ? $ionAuth->user()->row() : null;
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

            <?php if ($loggedIn): ?>
                <span class="text-sm text-slate-400 hidden sm:inline">
                    Hi, <?= esc($user->username ?? $user->email) ?>
                </span>

                <?php if ($isAdmin): ?>
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
                <a href="<?= site_url('auth/login') ?>"
                   class="px-4 py-1.5 rounded-md bg-indigo-500 hover:bg-indigo-400 transition text-sm font-medium">
                    Log in
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-6 py-8">
    <?php if (session()->getFlashdata('message')): ?>
        <div class="mb-4 p-4 rounded-md bg-emerald-500/20 border border-emerald-500 text-emerald-200">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 p-4 rounded-md bg-rose-500/20 border border-rose-500 text-rose-200">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<footer class="border-t border-slate-700 mt-12 py-6 text-center text-sm text-slate-500">
    CineDB &middot; built with CodeIgniter 4 + Tailwind
</footer>

</body>
</html>
