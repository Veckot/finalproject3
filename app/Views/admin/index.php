<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-8">
    <h1 class="text-3xl font-bold">Admin Panel</h1>
    <p class="text-slate-400 mt-1">Manage the movie catalogue.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">Movies</p>
        <p class="text-3xl font-bold mt-1"><?= esc($movie_count) ?></p>
    </div>
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">Genres</p>
        <p class="text-3xl font-bold mt-1"><?= esc($genre_count) ?></p>
    </div>
    <div class="bg-slate-800 rounded-lg p-5 border border-slate-700">
        <p class="text-slate-400 text-xs uppercase tracking-wide">People</p>
        <p class="text-3xl font-bold mt-1"><?= esc($person_count) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <a href="<?= site_url('admin/add') ?>"
       class="block bg-emerald-600 hover:bg-emerald-500 transition rounded-lg p-8 text-center group">
        <div class="text-5xl mb-3">+</div>
        <h2 class="text-xl font-bold">Add</h2>
        <p class="text-emerald-100 text-sm mt-1">Create a new entry</p>
    </a>

    <a href="<?= site_url('admin/list?entity=movie') ?>"
       class="block bg-indigo-600 hover:bg-indigo-500 transition rounded-lg p-8 text-center group">
        <div class="text-5xl mb-3">&#9998;</div>
        <h2 class="text-xl font-bold">Edit</h2>
        <p class="text-indigo-100 text-sm mt-1">Modify existing entries</p>
    </a>

    <a href="<?= site_url('admin/list?entity=movie') ?>"
       class="block bg-rose-600 hover:bg-rose-500 transition rounded-lg p-8 text-center group">
        <div class="text-5xl mb-3">&times;</div>
        <h2 class="text-xl font-bold">Remove</h2>
        <p class="text-rose-100 text-sm mt-1">Delete entries</p>
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
    <a href="<?= site_url('admin/users') ?>"
       class="block bg-slate-700 hover:bg-slate-600 transition rounded-lg p-8 text-center group">
        <div class="text-5xl mb-3">&#128100;</div>
        <h2 class="text-xl font-bold">Users</h2>
        <p class="text-slate-300 text-sm mt-1">Manage accounts &amp; roles</p>
    </a>
</div>

<?= $this->endSection() ?>
