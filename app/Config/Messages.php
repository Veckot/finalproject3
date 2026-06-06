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

    /** Shown after a successful registration. */
    public string $registerSuccess = 'Your account was created. You can now log in.';

    /** Shown when registration fails at the IonAuth level. */
    public string $registerFailed  = 'We could not create your account. Please try again.';

    /** Shown after a password-reset request (always, to avoid leaking accounts). */
    public string $resetSent       = 'If that account exists, a password-reset link has been sent.';

    /** Shown after a password is successfully reset. */
    public string $resetSuccess    = 'Your password has been reset. Please log in.';

    /** Shown when a reset link / code is invalid or expired. */
    public string $resetInvalidCode = 'That password-reset link is invalid or has expired.';

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
    // MOVIE <-> PEOPLE (cast & crew)
    // -------------------------------------------------------------------------

    /** %1$s = person name, %2$s = role */
    public string $personAttached  = '%1$s was added to the film as %2$s.';

    /** %s = person name */
    public string $personDetached  = '%s was removed from the film.';

    public string $personAttachInvalid = 'Please choose a valid person and role.';

    // -------------------------------------------------------------------------
    // USERS (Ion Auth account management)
    // -------------------------------------------------------------------------

    /** %s = username or email */
    public string $userUpdated     = 'User "%s" was updated.';

    /** %s = username or email */
    public string $userDeleted     = 'User "%s" was deleted.';

    public string $userNotFound    = 'That user could not be found.';

    /** Guard: an admin cannot delete their own account. */
    public string $userSelfDelete  = 'You cannot delete your own account while logged in.';

    /** Guard: validation problem when updating a user. */
    public string $userUpdateFailed = 'Could not update the user. Please check the fields.';

    // -------------------------------------------------------------------------
    // GENERIC / FALLBACKS
    // -------------------------------------------------------------------------

    public string $unknownEntity   = 'Unknown entity type. Please use the form.';

    public string $validationFailed = 'Please fix the highlighted errors and try again.';

    public string $unexpectedError  = 'Something went wrong. Please try again.';
}
