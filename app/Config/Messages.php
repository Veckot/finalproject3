<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Central store for every user-facing flash / alert message.
 *
 * Usage in controllers:
 *   $msg = config('Messages');
 *   return redirect()->back()->with('success', $msg->movieAdded);
 *
 * Keys are grouped by domain. Each entry is a plain string; dynamic
 * placeholders use sprintf() syntax (%s) where needed.
 */
class Messages extends BaseConfig
{
    // -------------------------------------------------------------------------
    // AUTH
    // -------------------------------------------------------------------------

    /** Shown after a successful login. */
    public string $loginSuccess    = 'Welcome back! You are now logged in.';

    /** Shown when login credentials are wrong (fallback if IonAuth gives nothing). */
    public string $loginFailed     = 'Invalid email / username or password. Please try again.';

    /** Shown after logout. */
    public string $logoutSuccess   = 'You have been logged out. See you soon!';

    /** Redirect message when unauthenticated user hits a protected page. */
    public string $loginRequired   = 'Please log in to access that page.';

    /** Redirect message when authenticated non-admin hits an admin page. */
    public string $adminRequired   = 'You need administrator privileges to access that area.';

    // -------------------------------------------------------------------------
    // MOVIE
    // -------------------------------------------------------------------------

    /** %s = movie name */
    public string $movieAdded      = '"%s" was added to the catalogue successfully.';

    /** %s = movie name */
    public string $movieUpdated    = '"%s" was updated successfully.';

    /** %s = movie name */
    public string $movieDeleted    = '"%s" was removed from the catalogue.';

    public string $movieNotFound   = 'That movie could not be found.';

    // -------------------------------------------------------------------------
    // GENRE
    // -------------------------------------------------------------------------

    /** %s = genre name */
    public string $genreAdded      = 'Genre "%s" was added successfully.';

    /** %s = genre name */
    public string $genreUpdated    = 'Genre "%s" was updated successfully.';

    /** %s = genre name */
    public string $genreDeleted    = 'Genre "%s" was removed.';

    public string $genreNotFound   = 'That genre could not be found.';

    public string $genreIdTaken    = 'A genre with that ID already exists.';

    // -------------------------------------------------------------------------
    // PERSON
    // -------------------------------------------------------------------------

    /** %s = person name */
    public string $personAdded     = '"%s" was added to the people list.';

    /** %s = person name */
    public string $personUpdated   = '"%s" was updated successfully.';

    /** %s = person name */
    public string $personDeleted   = '"%s" was removed from the people list.';

    public string $personNotFound  = 'That person could not be found.';

    // -------------------------------------------------------------------------
    // GENERIC / FALLBACKS
    // -------------------------------------------------------------------------

    public string $unknownEntity   = 'Unknown entity type. Please use the form.';

    public string $validationFailed = 'Please fix the highlighted errors and try again.';

    public string $unexpectedError  = 'Something went wrong. Please try again.';
}
