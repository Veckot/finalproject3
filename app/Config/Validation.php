<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    /**
     * Rules for new-account registration.
     *
     * @var array<string, array<string, string>>
     */
    public array $register = [
        'username' => [
            'label'  => 'Username',
            'rules'  => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
        ],
        'email' => [
            'label'  => 'Email',
            'rules'  => 'required|valid_email|is_unique[users.email]',
        ],
        'password' => [
            'label'  => 'Password',
            'rules'  => 'required|min_length[8]|max_length[255]',
        ],
        'pass_confirm' => [
            'label'  => 'Password confirmation',
            'rules'  => 'required|matches[password]',
        ],
    ];

    /**
     * Rules for resetting a password from a reset link.
     *
     * @var array<string, array<string, string>>
     */
    public array $reset_password = [
        'password' => [
            'label'  => 'Password',
            'rules'  => 'required|min_length[8]|max_length[255]',
        ],
        'pass_confirm' => [
            'label'  => 'Password confirmation',
            'rules'  => 'required|matches[password]',
        ],
    ];
}
