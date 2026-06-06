<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Edit User</h1>
        <p class="text-slate-400 mt-1">Account #<?= esc($user->id) ?></p>
    </div>
    <a href="<?= site_url('admin/users') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to users</a>
</div>

<div class="max-w-2xl bg-slate-800 rounded-lg p-6 border border-slate-700">
    <?= form_open(site_url('admin/users/' . $user->id), ['class' => 'space-y-4']) ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= field_text('username', 'Username', ['value' => old('username', $user->username), 'required' => true]) ?>
            <?= field_text('email', 'Email', ['type' => 'email', 'value' => old('email', $user->email), 'required' => true]) ?>
            <?= field_text('first_name', 'First name', ['value' => old('first_name', $user->first_name)]) ?>
            <?= field_text('last_name', 'Last name', ['value' => old('last_name', $user->last_name)]) ?>
        </div>

        <?= field_password('password', 'New password', [
            'help' => 'Leave blank to keep the current password. Minimum 8 characters.',
        ]) ?>

        <div class="flex flex-wrap gap-6 pt-2">
            <?= field_checkbox('active', 'Active (can log in)', ['checked' => ! empty($user->active)]) ?>
            <?= field_checkbox('is_admin', 'Administrator', ['checked' => $is_admin]) ?>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-700">
            <?= field_submit('Save changes') ?>
            <a href="<?= site_url('admin/users') ?>" class="px-5 py-2 rounded-md border border-slate-600 hover:bg-slate-700">Cancel</a>
        </div>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>
