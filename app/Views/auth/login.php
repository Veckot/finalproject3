<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto bg-slate-800 rounded-lg p-8 border border-slate-700 mt-6">
    <h1 class="text-2xl font-bold mb-1">Log in</h1>
    <p class="text-sm text-slate-400 mb-6">Use your email <em>or</em> username.</p>

    <?= form_open(site_url('auth/login'), ['class' => 'space-y-4']) ?>

        <?= field_text('login', 'Email or username', [
            'value'     => old('login'),
            'required'  => true,
            'attrs'     => ['autofocus' => 'autofocus', 'autocomplete' => 'username'],
        ]) ?>

        <?= field_password('password', 'Password', [
            'required' => true,
            'attrs'    => ['autocomplete' => 'current-password'],
        ]) ?>

        <?= field_checkbox('remember', 'Remember me') ?>

        <?= field_submit('Log in', ['class' => 'w-full px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 font-semibold cursor-pointer transition']) ?>

    <?= form_close() ?>

    <div class="flex items-center justify-between mt-6 text-sm">
        <a href="<?= site_url('auth/register') ?>" class="text-indigo-400 hover:text-indigo-300">Create account</a>
        <a href="<?= site_url('auth/forgot') ?>" class="text-slate-400 hover:text-slate-200">Forgot password?</a>
    </div>

    <p class="text-xs text-slate-500 mt-6">
        Seeded admin: <code class="text-slate-300">admin@admin.com / password</code>
    </p>
</div>

<?= $this->endSection() ?>
