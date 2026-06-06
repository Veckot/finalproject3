<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto bg-slate-800 rounded-lg p-8 border border-slate-700 mt-6">
    <h1 class="text-2xl font-bold mb-1">Reset password</h1>
    <p class="text-sm text-slate-400 mb-6">Choose a new password for your account.</p>

    <?= form_open(site_url('auth/reset/' . $code), ['class' => 'space-y-4']) ?>

        <?= field_password('password', 'New password', [
            'required' => true,
            'help'     => 'At least 8 characters.',
            'attrs'    => ['autocomplete' => 'new-password'],
        ]) ?>

        <?= field_password('pass_confirm', 'Confirm new password', [
            'required' => true,
            'attrs'    => ['autocomplete' => 'new-password'],
        ]) ?>

        <?= field_submit('Reset password', ['class' => 'w-full px-4 py-2 rounded-md bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold cursor-pointer transition']) ?>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>
