<?php
/**
 * TinyMCE rich-text editor (self-hosted, no CDN).
 *
 * Served straight from the npm package in node_modules/ (the project root is
 * the web root, so the file is reachable). Loaded only on pages that need it
 * (movie add/edit) via the layout's `scripts` section. Initialises on the
 * #description textarea using the dark skin so it matches the site theme.
 * `license_key: 'gpl'` selects the free GPL licence shipped with the package.
 */
?>
<script src="<?= base_url('node_modules/tinymce/tinymce.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.tinymce === 'undefined') {
            return;
        }
        tinymce.init({
            selector: '#description',
            license_key: 'gpl',
            promotion: false,
            branding: false,
            menubar: false,
            height: 320,
            skin: 'oxide-dark',
            content_css: 'dark',
            plugins: 'lists link autolink charmap wordcount',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat'
        });
    });
</script>
