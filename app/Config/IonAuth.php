<?php namespace Config;

class IonAuth extends \IonAuth\Config\IonAuth
{
    // set your specific config
    // public $siteTitle                = 'Example.com';       // Site Title, example.com
    // public $adminEmail               = 'admin@example.com'; // Admin Email, admin@example.com
    // public $emailTemplates           = 'App\\Views\\auth\\email\\';

    /**
     * Login lockout / throttling.
     *
     * Disabled so repeated login attempts during development are never
     * locked out or timed out. Re-enable trackLoginAttempts in production
     * if you want brute-force protection.
     */
    public $trackLoginAttempts   = false; // do not count failed attempts -> no lockout
    public $maximumLoginAttempts = 0;     // 0 = unlimited attempts
    public $lockoutTime          = 0;     // no lockout window
}
