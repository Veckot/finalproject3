<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php helper(['form', 'url']); ?>

<div class="max-w-md mx-auto bg-slate-800 rounded-lg p-8 border border-slate-700 mt-10">
    <h1 class="text-2xl font-bold mb-1">Log in</h1>
    <p class="text-sm text-slate-400 mb-6">Use your email or username.</p>

    <?= form_open(site_url('auth/login'), ['class' => 'space-y-4']) ?>

    <div>
        <?= form_label('Email or username', 'identity', ['class' => 'block text-sm font-medium mb-1']) ?>
        <?= form_input([
            'name'  => 'identity',
            'id'    => 'identity',
            'value' => old('identity'),
            'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
            'autofocus' => 'autofocus',
            'required' => 'required',
        ]) ?>
    </div>

    <div>
        <?= form_label('Password', 'password', ['class' => 'block text-sm font-medium mb-1']) ?>
        <?= form_password([
            'name'  => 'password',
            'id'    => 'password',
            'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
            'required' => 'required',
        ]) ?>
    </div>

    <div class="flex items-center gap-2">
        <?= form_checkbox('remember', '1', false, 'id="remember" class="accent-indigo-500"') ?>
        <?= form_label('Remember me', 'remember', ['class' => 'text-sm text-slate-300']) ?>
    </div>

    <?= form_submit('submit', 'Log in', [
        'class' => 'w-full px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 font-semibold cursor-pointer',
    ]) ?>

    <?= form_close() ?>
    
</div>

<?= $this->endSection() ?>
