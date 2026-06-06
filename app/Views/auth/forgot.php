<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto bg-slate-800 rounded-lg p-8 border border-slate-700 mt-6">
    <h1 class="text-2xl font-bold mb-1">Forgot password</h1>
    <p class="text-sm text-slate-400 mb-6">
        Enter your email or username and we'll send a reset link.
    </p>

    <?= form_open(site_url('auth/forgot'), ['class' => 'space-y-4']) ?>

        <?= field_text('login', 'Email or username', [
            'value'    => old('login'),
            'required' => true,
            'attrs'    => ['autofocus' => 'autofocus'],
        ]) ?>

        <?= field_submit('Send reset link', ['class' => 'w-full px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 font-semibold cursor-pointer transition']) ?>

    <?= form_close() ?>

    <p class="text-sm text-slate-400 mt-6">
        <a href="<?= site_url('auth/login') ?>" class="text-indigo-400 hover:text-indigo-300">&larr; Back to login</a>
    </p>
</div>

<?= $this->endSection() ?>
