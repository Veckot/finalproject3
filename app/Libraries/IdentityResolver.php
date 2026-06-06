<?php

namespace App\Libraries;

use Config\Database;

/**
 * Resolves a user's login input (which may be either an email address or a
 * username) into the identity value IonAuth expects for authentication.
 *
 * IonAuth authenticates against a single configured identity column (by
 * default `email`). This library lets the login form accept *either* an email
 * or a username by translating a username into the corresponding identity
 * value before handing it to IonAuth.
 */
class IdentityResolver
{
    /**
     * Translate a raw login string into the IonAuth identity value.
     *
     * If the input is a valid email it is returned unchanged. Otherwise it is
     * treated as a username and the matching account's identity-column value is
     * looked up in the users table.
     *
     * @param string $login Raw login input typed by the user (email or username).
     * @return string|null   The identity value to pass to IonAuth::login(),
     *                        or null when no matching account exists.
     */
    public function to_identity(string $login): ?string
    {
        $login = trim($login);
        if ($login === '') {
            return null;
        }

        // A valid email is already a usable identity (default identity column).
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return $login;
        }

        $config          = config('IonAuth');
        $identity_column = $config->identity;
        $group           = $config->databaseGroupName ?: null;

        $db  = Database::connect($group);
        $row = $db->table($config->tables['users'])
            ->select($identity_column)
            ->where('username', $login)
            ->get()
            ->getRow();

        return $row->{$identity_column} ?? null;
    }
}
