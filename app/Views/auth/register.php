<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto bg-slate-800 rounded-lg p-8 border border-slate-700 mt-6">
    <h1 class="text-2xl font-bold mb-1">Create account</h1>
    <p class="text-sm text-slate-400 mb-6">Join CineDB. All fields are required.</p>

    <?= form_open(site_url('auth/register'), ['class' => 'space-y-4']) ?>

        <?= field_text('username', 'Username', [
            'value'    => old('username'),
            'required' => true,
            'attrs'    => ['autocomplete' => 'username'],
        ]) ?>

        <?= field_text('email', 'Email', [
            'type'     => 'email',
            'value'    => old('email'),
            'required' => true,
            'attrs'    => ['autocomplete' => 'email'],
        ]) ?>

        <?= field_password('password', 'Password', [
            'required' => true,
            'help'     => 'At least 8 characters.',
            'attrs'    => ['autocomplete' => 'new-password'],
        ]) ?>

        <?= field_password('pass_confirm', 'Confirm password', [
            'required' => true,
            'attrs'    => ['autocomplete' => 'new-password'],
        ]) ?>

        <?= field_submit('Create account', ['class' => 'w-full px-4 py-2 rounded-md bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold cursor-pointer transition']) ?>

    <?= form_close() ?>

    <p class="text-sm text-slate-400 mt-6">
        Already have an account?
        <a href="<?= site_url('auth/login') ?>" class="text-indigo-400 hover:text-indigo-300">Log in</a>
    </p>
</div>

<?= $this->endSection() ?>
