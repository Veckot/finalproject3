<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
helper(['form', 'url']);

$val = function (string $key, $default = '') use ($item) {
    return old($key, $item->{$key} ?? $default);
};
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold"><?= esc($title) ?></h1>
        <p class="text-slate-400 mt-1">Entry #<?= esc($item->id) ?></p>
    </div>
    <a href="<?= site_url('admin/list?entity=' . $entity) ?>" class="text-sm text-slate-400 hover:text-slate-200">&larr; Back to list</a>
</div>

<div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
    <?= form_open(site_url('admin/update/' . $entity . '/' . $item->id), ['class' => 'space-y-4']) ?>

    <?php if ($entity === 'movie'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <?= form_label('Title', 'name', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'name', 'id' => 'name', 'value' => $val('name'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500', 'required' => 'required']) ?>
            </div>
            <div>
                <?= form_label('Original Title', 'original_title', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'original_title', 'id' => 'original_title', 'value' => $val('original_title'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Original Language', 'original_language', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'original_language', 'id' => 'original_language', 'value' => $val('original_language'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Release Date', 'release_date', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['type' => 'date', 'name' => 'release_date', 'id' => 'release_date', 'value' => $val('release_date'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Runtime', 'runtime', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['type' => 'number', 'name' => 'runtime', 'id' => 'runtime', 'value' => $val('runtime'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Rating', 'rating', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['type' => 'number', 'step' => '0.1', 'name' => 'rating', 'id' => 'rating', 'value' => $val('rating'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Status', 'status', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_dropdown(
                    'status',
                    ['' => '— none —', 'Released' => 'Released', 'In Production' => 'In Production', 'Post Production' => 'Post Production', 'Planned' => 'Planned'],
                    $val('status'),
                    ['class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']
                ) ?>
            </div>
            <div>
                <?= form_label('Poster URL', 'pic', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'pic', 'id' => 'pic', 'value' => $val('pic'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
        </div>
        <div>
            <?= form_label('Description', 'description', ['class' => 'block text-sm font-medium mb-1']) ?>
            <?= form_textarea(['name' => 'description', 'id' => 'description', 'value' => $val('description'), 'rows' => 4,
                'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
        </div>
        <div class="flex items-center gap-2">
            <?= form_checkbox('adult', '1', !empty($item->adult), 'id="adult" class="accent-indigo-500"') ?>
            <?= form_label('Adult content', 'adult', ['class' => 'text-sm']) ?>
        </div>

    <?php elseif ($entity === 'genre'): ?>
        <div>
            <?= form_label('Name', 'name', ['class' => 'block text-sm font-medium mb-1']) ?>
            <?= form_input(['name' => 'name', 'id' => 'name', 'value' => $val('name'),
                'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500', 'required' => 'required']) ?>
        </div>

    <?php elseif ($entity === 'person'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <?= form_label('Name', 'name', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'name', 'id' => 'name', 'value' => $val('name'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500', 'required' => 'required']) ?>
            </div>
            <div>
                <?= form_label('Gender', 'gender', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_dropdown(
                    'gender',
                    ['' => '— unknown —', '1' => 'Female', '2' => 'Male', '3' => 'Non-binary'],
                    $val('gender'),
                    ['class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']
                ) ?>
            </div>
            <div>
                <?= form_label('Birthday', 'birthday', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['type' => 'date', 'name' => 'birthday', 'id' => 'birthday', 'value' => $val('birthday'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Deathday', 'deathday', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['type' => 'date', 'name' => 'deathday', 'id' => 'deathday', 'value' => $val('deathday'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Place of Birth', 'place_of_birth', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'place_of_birth', 'id' => 'place_of_birth', 'value' => $val('place_of_birth'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div>
                <?= form_label('Known for department', 'known_for_department', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'known_for_department', 'id' => 'known_for_department', 'value' => $val('known_for_department'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
            <div class="md:col-span-2">
                <?= form_label('Profile path', 'profile_path', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_input(['name' => 'profile_path', 'id' => 'profile_path', 'value' => $val('profile_path'),
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500']) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex gap-3 pt-4 border-t border-slate-700">
        <?= form_submit('submit', 'Save changes', ['class' => 'px-5 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 font-semibold cursor-pointer']) ?>
        <a href="<?= site_url('admin/list?entity=' . $entity) ?>" class="px-5 py-2 rounded-md border border-slate-600 hover:bg-slate-700">
            Cancel
        </a>
    </div>

    <?= form_close() ?>
</div>

<?php if ($entity === 'movie'): ?>
    <?php
    // Build the people dropdown: id => name
    $people_options = ['' => '— select a person —'];
    foreach ($all_people as $person) {
        $people_options[$person->id] = $person->name;
    }
    ?>
    <div class="bg-slate-800 rounded-lg p-6 border border-slate-700 mt-6">
        <h2 class="text-xl font-bold mb-1">Cast &amp; Crew</h2>
        <p class="text-slate-400 text-sm mb-4">Add or remove the people involved in this film.</p>

        <!-- Currently attached -->
        <?php if (empty($attached_people)): ?>
            <p class="text-slate-500 text-sm mb-6">No people are linked to this film yet.</p>
        <?php else: ?>
            <ul class="divide-y divide-slate-700 mb-6 border border-slate-700 rounded-md overflow-hidden">
                <?php foreach ($attached_people as $p): ?>
                    <li class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-900/40">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="font-medium truncate"><?= esc($p->name) ?></span>
                            <span class="px-2 py-0.5 rounded-full bg-slate-700 text-xs capitalize">
                                <?= esc($p->role) ?>
                            </span>
                        </div>
                        <?= form_open(site_url('admin/movie/' . $item->id . '/people/' . $p->id . '/detach'), [
                            'class'    => 'inline shrink-0',
                            'onsubmit' => "return confirm('Remove " . esc($p->name, 'js') . " from this film?');",
                        ]) ?>
                            <button type="submit"
                                    class="px-3 py-1 rounded bg-rose-600 hover:bg-rose-500 text-xs font-medium">
                                Remove
                            </button>
                        <?= form_close() ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Add a person -->
        <?= form_open(site_url('admin/movie/' . $item->id . '/people'), [
            'class' => 'flex flex-col sm:flex-row gap-3 sm:items-end',
        ]) ?>
            <div class="flex-1">
                <?= form_label('Person', 'people_id', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_dropdown('people_id', $people_options, old('people_id'), [
                    'id'    => 'people_id',
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                    'required' => 'required',
                ]) ?>
            </div>
            <div class="sm:w-48">
                <?= form_label('Role', 'role', ['class' => 'block text-sm font-medium mb-1']) ?>
                <?= form_dropdown('role', $roles, old('role', 'actor'), [
                    'id'    => 'role',
                    'class' => 'w-full px-3 py-2 rounded-md bg-slate-900 border border-slate-600 focus:outline-none focus:border-indigo-500',
                ]) ?>
            </div>
            <?= form_submit('attach', 'Add person', [
                'class' => 'px-5 py-2 rounded-md bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold cursor-pointer',
            ]) ?>
        <?= form_close() ?>

        <p class="text-xs text-slate-500 mt-3">
            Need someone who isn't listed?
            <a href="<?= site_url('admin/add?entity=person') ?>" class="text-indigo-400 hover:text-indigo-300 underline">
                Add a new person
            </a> first.
        </p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?php if ($entity === 'movie'): ?>
    <?= $this->section('scripts') ?>
        <?= view('partials/_tinymce') ?>
    <?= $this->endSection() ?>
<?php endif; ?>
