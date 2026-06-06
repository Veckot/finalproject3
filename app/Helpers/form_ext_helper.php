<?php

/**
 * Extended form helper.
 *
 * A thin layer on top of CodeIgniter's built-in form helper that emits
 * Tailwind-styled, fully-formatted form controls (label + control + inline
 * validation error) so views don't repeat the same long class strings.
 *
 * All functions are namespaced into the global scope (CI4 helper convention)
 * and are guarded with function_exists() so the file can be loaded once.
 *
 * Every public helper is documented with: what it does, its parameters and
 * its return value.
 */

use Config\Services;

if (! function_exists('field_shell')) {
    /**
     * Internal: wrap a label + control + error block in consistent markup.
     *
     * @param string $label   Human-readable field label (empty string = no label).
     * @param string $for      The control's id, used for the label's "for" attribute.
     * @param string $control Pre-rendered HTML of the input/select/textarea.
     * @param string $name     Field name, used to look up validation errors.
     * @param string $help     Optional small helper text shown under the control.
     * @param string $wrapper Extra CSS classes for the outer wrapper <div>.
     * @return string          The complete field group HTML.
     */
    function field_shell(string $label, string $for, string $control, string $name, string $help = '', string $wrapper = ''): string
    {
        $label_html = $label !== ''
            ? '<label for="' . esc($for, 'attr') . '" class="block text-sm font-medium mb-1">' . esc($label) . '</label>'
            : '';

        $help_html = $help !== ''
            ? '<p class="text-xs text-slate-400 mt-1">' . esc($help) . '</p>'
            : '';

        return '<div class="' . trim('field ' . $wrapper) . '">'
            . $label_html
            . $control
            . field_errors($name)
            . $help_html
            . '</div>';
    }
}

if (! function_exists('field_input_classes')) {
    /**
     * Internal: the shared Tailwind class string for text-like controls.
     *
     * Adds a red border automatically when the named field has a validation error.
     *
     * @param string $name Field name (to detect validation errors).
     * @return string       The class attribute value.
     */
    function field_input_classes(string $name): string
    {
        $base = 'w-full px-3 py-2 rounded-md bg-slate-900 border focus:outline-none transition';
        $state = field_has_error($name)
            ? ' border-rose-500 focus:border-rose-400'
            : ' border-slate-600 focus:border-indigo-500';

        return $base . $state;
    }
}

if (! function_exists('field_has_error')) {
    /**
     * Internal: whether the given field currently has a validation error.
     *
     * Looks in flashed 'errors' (after a redirect) and in the live validator.
     *
     * @param string $name Field name.
     * @return bool         True if an error exists for this field.
     */
    function field_has_error(string $name): bool
    {
        $flashed = session('errors');
        if (is_array($flashed) && array_key_exists($name, $flashed)) {
            return true;
        }

        $validation = Services::validation();

        return $validation->hasError($name);
    }
}

if (! function_exists('field_errors')) {
    /**
     * Render the validation error(s) for a single field, if any.
     *
     * @param string $name Field name.
     * @return string       A small red error paragraph, or '' when no error.
     */
    function field_errors(string $name): string
    {
        $message = '';

        $flashed = session('errors');
        if (is_array($flashed) && ! empty($flashed[$name])) {
            $message = $flashed[$name];
        } else {
            $validation = Services::validation();
            if ($validation->hasError($name)) {
                $message = $validation->getError($name);
            }
        }

        if ($message === '') {
            return '';
        }

        return '<p class="text-xs text-rose-400 mt-1">' . esc($message) . '</p>';
    }
}

if (! function_exists('field_text')) {
    /**
     * Render a labelled text-style input field group.
     *
     * @param string $name  Field name (and id).
     * @param string $label Field label.
     * @param array  $opts  Optional keys: value, type, placeholder, required (bool),
     *                       help, attrs (array of extra attributes), wrapper (css), id.
     * @return string        The complete Tailwind field group.
     */
    function field_text(string $name, string $label, array $opts = []): string
    {
        helper('form');

        $id    = $opts['id'] ?? $name;
        $value = $opts['value'] ?? old($name);

        $attrs = array_merge([
            'name'  => $name,
            'id'    => $id,
            'type'  => $opts['type'] ?? 'text',
            'value' => $value,
            'class' => field_input_classes($name),
        ], $opts['attrs'] ?? []);

        if (! empty($opts['placeholder'])) {
            $attrs['placeholder'] = $opts['placeholder'];
        }
        if (! empty($opts['required'])) {
            $attrs['required'] = 'required';
        }

        $control = form_input($attrs);

        return field_shell($label, $id, $control, $name, $opts['help'] ?? '', $opts['wrapper'] ?? '');
    }
}

if (! function_exists('field_password')) {
    /**
     * Render a password field with a built-in show/hide toggle button.
     *
     * Relies on assets/js/app.js handling clicks on [data-toggle-password].
     *
     * @param string $name  Field name (and id).
     * @param string $label Field label.
     * @param array  $opts  Optional keys: required (bool), placeholder, help, wrapper, id.
     * @return string        The complete field group with toggle.
     */
    function field_password(string $name, string $label, array $opts = []): string
    {
        helper('form');

        $id = $opts['id'] ?? $name;

        $attrs = [
            'name'  => $name,
            'id'    => $id,
            'type'  => 'password',
            'class' => field_input_classes($name) . ' pr-10',
        ];
        if (! empty($opts['placeholder'])) {
            $attrs['placeholder'] = $opts['placeholder'];
        }
        if (! empty($opts['required'])) {
            $attrs['required'] = 'required';
        }

        $input  = form_input($attrs);
        $toggle = '<button type="button" data-toggle-password="' . esc($id, 'attr') . '"'
            . ' class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-200"'
            . ' aria-label="Show or hide password" tabindex="-1">'
            . '<span data-eye>&#128065;</span>'
            . '</button>';

        $control = '<div class="relative">' . $input . $toggle . '</div>';

        return field_shell($label, $id, $control, $name, $opts['help'] ?? '', $opts['wrapper'] ?? '');
    }
}

if (! function_exists('field_textarea')) {
    /**
     * Render a labelled textarea field group.
     *
     * @param string $name  Field name (and id).
     * @param string $label Field label.
     * @param array  $opts  Optional keys: value, rows, placeholder, required, help, wrapper, id.
     * @return string        The complete field group.
     */
    function field_textarea(string $name, string $label, array $opts = []): string
    {
        helper('form');

        $id    = $opts['id'] ?? $name;
        $value = $opts['value'] ?? old($name);

        $attrs = [
            'name'  => $name,
            'id'    => $id,
            'rows'  => $opts['rows'] ?? 4,
            'value' => $value,
            'class' => field_input_classes($name),
        ];
        if (! empty($opts['placeholder'])) {
            $attrs['placeholder'] = $opts['placeholder'];
        }
        if (! empty($opts['required'])) {
            $attrs['required'] = 'required';
        }

        $control = form_textarea($attrs);

        return field_shell($label, $id, $control, $name, $opts['help'] ?? '', $opts['wrapper'] ?? '');
    }
}

if (! function_exists('field_select')) {
    /**
     * Render a labelled <select> field group.
     *
     * @param string $name    Field name (and id). Use "name[]" for multiple.
     * @param string $label   Field label.
     * @param array  $options Map of value => label for the options.
     * @param array  $opts    Optional keys: selected (mixed), multiple (bool),
     *                         required, help, wrapper, id, attrs (extra attributes).
     * @return string          The complete field group.
     */
    function field_select(string $name, string $label, array $options, array $opts = []): string
    {
        helper('form');

        $id       = $opts['id'] ?? rtrim($name, '[]');
        $selected = $opts['selected'] ?? old(rtrim($name, '[]'));

        $extra = array_merge([
            'id'    => $id,
            'class' => field_input_classes($name),
        ], $opts['attrs'] ?? []);

        if (! empty($opts['multiple'])) {
            $extra['multiple'] = 'multiple';
        }
        if (! empty($opts['required'])) {
            $extra['required'] = 'required';
        }

        $control = form_dropdown($name, $options, $selected, $extra);

        return field_shell($label, $id, $control, rtrim($name, '[]'), $opts['help'] ?? '', $opts['wrapper'] ?? '');
    }
}

if (! function_exists('field_checkbox')) {
    /**
     * Render a single inline checkbox with a label to its right.
     *
     * @param string $name    Field name (and id).
     * @param string $label   Label text shown next to the box.
     * @param array  $opts    Optional keys: value (submit value, default '1'),
     *                         checked (bool), wrapper (css), id.
     * @return string          The complete inline checkbox group.
     */
    function field_checkbox(string $name, string $label, array $opts = []): string
    {
        helper('form');

        $id      = $opts['id'] ?? $name;
        $value   = $opts['value'] ?? '1';
        $checked = ! empty($opts['checked']);

        $box   = form_checkbox($name, $value, $checked, 'id="' . esc($id, 'attr') . '" class="accent-indigo-500"');
        $lbl   = '<label for="' . esc($id, 'attr') . '" class="text-sm">' . esc($label) . '</label>';

        return '<div class="' . trim('flex items-center gap-2 ' . ($opts['wrapper'] ?? '')) . '">'
            . $box . $lbl . '</div>';
    }
}

if (! function_exists('field_submit')) {
    /**
     * Render a primary submit button.
     *
     * @param string $text  Button caption.
     * @param array  $opts  Optional keys: name (default 'submit'), class (override).
     * @return string        The submit button HTML.
     */
    function field_submit(string $text, array $opts = []): string
    {
        helper('form');

        $class = $opts['class']
            ?? 'px-5 py-2 rounded-md bg-indigo-500 hover:bg-indigo-400 font-semibold cursor-pointer transition';

        return form_submit($opts['name'] ?? 'submit', $text, ['class' => $class]);
    }
}
