<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
helper(['form', 'url']);
$errors = session()->getFlashdata('errors') ?? [];
$old    = function ($k, $default = '') {
    return old($k, $default);
};
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Add Entry</h1>
        <p class="text-slate-400 mt-1">Pick a type and fill in the fields.</p>
    </div>
    <a href="<?= site_url('admin') ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to panel</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="mb-4 p-4 rounded-md bg-rose-500/20 border border-rose-500 text-rose-200">
        <p class="font-semibold mb-1">Please fix the errors below:</p>
        <ul class="list-disc list-inside text-sm">
            <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="bg-slate-800 rounded-lg p-6 border border-slate-700 mb-6">
    <p class="text-sm font-semibold mb-3 text-slate-300">What do you want to add?</p>
    <form method="get" action="<?= site_url('admin/add') ?>" id="entityPicker" class="flex flex-wrap gap-4">
        <?php foreach ([
            'movie'  => 'Movie',
            'genre'  => 'Genre',
            'person' => 'Person',
        ] as $val => $label):
            $checked = ($entity === $val); ?>
            <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-md border <?= $checked ? 'bg-indigo-500 border-indigo-400 text-white' : 'bg-slate-900 border-slate-600 hover:bg-slate-700' ?>">
                <input type="radio" name="entity" value="<?= $val ?>" <?= $checked ? 'checked' : '' ?>
                       onchange="document.getElementById('entityPicker').submit()"
                       class="accent-indigo-500">
                <span class="font-medium"><?= $label ?></span>
            </label>
        <?php endforeach; ?>
    </form>
</div>

<div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
    <?= form_open(site_url('admin/store'), ['class' => 'space-y-4']) ?>
    <?= form_hidden('entity', $entity) ?>

    <?php if ($entity === 'movie'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <?= form_label('Title', 'name', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'name',
                    'id'    => 'name',
                    'value' => $old('name'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'required' => 'required',
                ]) ?>
            </div>
            <div>
                <?= form_label('Original Title', 'original_title', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'original_title',
                    'id'    => 'original_title',
                    'value' => $old('original_title'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'required' => 'required',
                ]) ?>
            </div>
            <div>
                <?= form_label('Original Language (ISO)', 'original_language', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'original_language',
                    'id'    => 'original_language',
                    'value' => $old('original_language', 'en'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'maxlength' => 10,
                    'required' => 'required',
                ]) ?>
            </div>
            <div>
                <?= form_label('Release Date', 'release_date', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'type'  => 'date',
                    'name'  => 'release_date',
                    'id'    => 'release_date',
                    'value' => $old('release_date'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                ]) ?>
            </div>
            <div>
                <?= form_label('Runtime (min)', 'runtime', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'type'  => 'number',
                    'name'  => 'runtime',
                    'id'    => 'runtime',
                    'value' => $old('runtime'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'min'   => 0,
                ]) ?>
            </div>
            <div>
                <?= form_label('Rating (0-10)', 'rating', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'type'  => 'number',
                    'step'  => '0.1',
                    'name'  => 'rating',
                    'id'    => 'rating',
                    'value' => $old('rating'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'min'   => 0,
                    'max'   => 10,
                ]) ?>
            </div>
            <div>
                <?= form_label('Status', 'status', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_dropdown(
                    'status',
                    ['' => '— none —', 'Released' => 'Released', 'In Production' => 'In Production', 'Post Production' => 'Post Production', 'Planned' => 'Planned'],
                    $old('status'),
                    ['class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']
                ) ?>
            </div>
            <div>
                <?= form_label('Poster URL', 'pic', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'pic',
                    'id'    => 'pic',
                    'value' => $old('pic'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'placeholder' => 'https://...',
                ]) ?>
            </div>
        </div>

        <div>
            <?= form_label('Description', 'description', ['class' => 'block text-sm font-medium mb-1']) ?>
            <?= form_textarea([
                'name'  => 'description',
                'id'    => 'description',
                'value' => $old('description'),
                'rows'  => 4,
                'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
            ]) ?>
        </div>

        <div class="flex items-center gap-2">
            <?= form_checkbox(['name' => 'adult', 'id' => 'adult', 'value' => '1', 'class' => 'accent-indigo-500']) ?>
            <?= form_label('Adult content', 'adult', ['class' => 'text-sm']) ?>
        </div>

    <?php elseif ($entity === 'genre'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <?= form_label('Genre ID', 'id', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'type'  => 'number',
                    'name'  => 'id',
                    'id'    => 'id',
                    'value' => $old('id'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'required' => 'required',
                    'min'   => 1,
                ]) ?>
                <p class="text-xs text-slate-400 mt-1">Numeric id (must be unique).</p>
            </div>
            <div>
                <?= form_label('Name', 'name', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'name',
                    'id'    => 'name',
                    'value' => $old('name'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'required' => 'required',
                ]) ?>
            </div>
        </div>

    <?php elseif ($entity === 'person'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <?= form_label('Name', 'name', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'name',
                    'id'    => 'name',
                    'value' => $old('name'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'required' => 'required',
                ]) ?>
            </div>
            <div>
                <?= form_label('Gender', 'gender', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_dropdown(
                    'gender',
                    ['' => '— unknown —', '1' => 'Female', '2' => 'Male', '3' => 'Non-binary'],
                    $old('gender'),
                    ['class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']
                ) ?>
            </div>
            <div>
                <?= form_label('Birthday', 'birthday', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'type'  => 'date',
                    'name'  => 'birthday',
                    'id'    => 'birthday',
                    'value' => $old('birthday'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                ]) ?>
            </div>
            <div>
                <?= form_label('Deathday', 'deathday', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'type'  => 'date',
                    'name'  => 'deathday',
                    'id'    => 'deathday',
                    'value' => $old('deathday'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                ]) ?>
            </div>
            <div>
                <?= form_label('Place of Birth', 'place_of_birth', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'place_of_birth',
                    'id'    => 'place_of_birth',
                    'value' => $old('place_of_birth'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                ]) ?>
            </div>
            <div>
                <?= form_label('Known for department', 'known_for_department', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'known_for_department',
                    'id'    => 'known_for_department',
                    'value' => $old('known_for_department'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'placeholder' => 'Acting, Directing, ...',
                ]) ?>
            </div>
            <div class="md:col-span-2">
                <?= form_label('Profile path / image URL', 'profile_path', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input([
                    'name'  => 'profile_path',
                    'id'    => 'profile_path',
                    'value' => $old('profile_path'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex gap-3 pt-4 border-t border-slate-700">
        <?= form_submit([
            'name'  => 'submit',
            'value' => 'Save ' . ucfirst($entity),
            'class' => 'px-5 py-2 rounded-md bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold cursor-pointer',
        ]) ?>
        <a href="<?= site_url('admin') ?>" class="px-5 py-2 rounded-md border border-slate-600 hover:bg-slate-700">
            Cancel
        </a>
    </div>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>
