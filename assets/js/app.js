/**
 * CineDB front-end behaviour.
 *
 * Plain vanilla JS, no framework. Loaded with `defer` from the layout so the
 * DOM is ready when this runs. Uses event delegation so it also works for
 * elements added dynamically (e.g. batch-add rows).
 */
(function () {
    'use strict';

    /**
     * Show / hide password toggle.
     * Any <button data-toggle-password="INPUT_ID"> flips that input between
     * type=password and type=text and toggles a struck-through eye glyph.
     */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-toggle-password]');
        if (!btn) return;

        const input = document.getElementById(btn.getAttribute('data-toggle-password'));
        if (!input) return;

        const eye = btn.querySelector('[data-eye]');
        if (input.type === 'password') {
            input.type = 'text';
            if (eye) eye.style.opacity = '0.4';
        } else {
            input.type = 'password';
            if (eye) eye.style.opacity = '1';
        }
    });

    /**
     * Batch-add rows.
     * <button data-add-row="TEMPLATE_ID" data-target="CONTAINER_ID"> clones the
     * <template> content into the container. Any [data-remove-row] inside a row
     * removes its closest [data-row].
     */
    document.addEventListener('click', function (e) {
        const addBtn = e.target.closest('[data-add-row]');
        if (addBtn) {
            const tpl = document.getElementById(addBtn.getAttribute('data-add-row'));
            const target = document.getElementById(addBtn.getAttribute('data-target'));
            if (tpl && target) {
                target.appendChild(tpl.content.cloneNode(true));
            }
            return;
        }

        const removeBtn = e.target.closest('[data-remove-row]');
        if (removeBtn) {
            const row = removeBtn.closest('[data-row]');
            if (row) row.remove();
        }
    });

    /**
     * Bulk-select on admin list.
     * A header checkbox [data-check-all] toggles every [data-row-check]; the
     * bulk action bar [data-bulk-bar] is shown only while at least one row is
     * checked.
     */
    document.addEventListener('change', function (e) {
        const all = e.target.closest('[data-check-all]');
        if (all) {
            document.querySelectorAll('[data-row-check]').forEach(function (cb) {
                cb.checked = all.checked;
            });
        }
        if (all || e.target.closest('[data-row-check]')) {
            refreshBulkBar();
        }
    });

    /**
     * Show or hide the bulk-action bar depending on selection count.
     * @returns {void}
     */
    function refreshBulkBar() {
        const bar = document.querySelector('[data-bulk-bar]');
        if (!bar) return;
        const count = document.querySelectorAll('[data-row-check]:checked').length;
        bar.classList.toggle('hidden', count === 0);
        const label = bar.querySelector('[data-bulk-count]');
        if (label) label.textContent = count;
    }

    /**
     * Initialise Select2 on any [data-select2] element when the library is
     * present. Dark-theme styling is provided by our CSS; Select2 itself is
     * loaded locally (no CDN) before this script when needed.
     * @returns {void}
     */
    function initSelect2() {
        if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 === 'undefined') {
            return;
        }
        window.jQuery('[data-select2]').select2({
            width: '100%',
            placeholder: 'Select…'
        });
    }

    document.addEventListener('DOMContentLoaded', initSelect2);
})();
