<?php

namespace App\Controllers;

use App\Libraries\IdentityResolver;
use Config\Messages;

/**
 * Auth
 * ====
 * Authentication front-end built on top of the Ion Auth library.
 *
 * Methods are grouped by area:
 *   1) Login / logout
 *   2) Registration
 *   3) Forgotten password (request link + reset with code)
 */
class Auth extends BaseController
{
    /** Ion Auth library instance. */
    protected \IonAuth\Libraries\IonAuth $ion_auth;

    /** User-facing text (flash messages). */
    protected Messages $msg;

    public function __construct()
    {
        $this->ion_auth = new \IonAuth\Libraries\IonAuth();
        $this->msg      = config('Messages');
    }

    // =========================================================================
    // 1) LOGIN / LOGOUT
    // =========================================================================

    /**
     * GET: show the login form. POST: authenticate.
     *
     * The login field accepts either an email or a username; it is resolved to
     * the Ion Auth identity by IdentityResolver before authenticating.
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function login()
    {
        if ($this->ion_auth->loggedIn()) {
            return redirect()->to(site_url('/'));
        }

        if ($this->request->is('post')) {
            $login    = (string) $this->request->getPost('login');
            $password = (string) $this->request->getPost('password');
            $remember = (bool)   $this->request->getPost('remember');

            $identity = (new IdentityResolver())->to_identity($login);

            if ($identity !== null && $this->ion_auth->login($identity, $password, $remember)) {
                return redirect()->to(site_url('/'))
                    ->with('success', $this->msg->loginSuccess);
            }

            $ion_error = strip_tags($this->ion_auth->errors());

            return redirect()->back()->withInput()
                ->with('error', $ion_error ?: $this->msg->loginFailed);
        }

        return view('auth/login', [
            'title'  => 'Log in',
            'crumbs' => [
                ['label' => 'Home',   'url' => site_url('/')],
                ['label' => 'Log in', 'url' => null],
            ],
        ]);
    }

    /**
     * Log the current user out.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        $this->ion_auth->logout();

        return redirect()->to(site_url('/'))
            ->with('success', $this->msg->logoutSuccess);
    }

    // =========================================================================
    // 2) REGISTRATION
    // =========================================================================

    /**
     * GET: show the registration form. POST: validate and create the account.
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function register()
    {
        if ($this->ion_auth->loggedIn()) {
            return redirect()->to(site_url('/'));
        }

        if ($this->request->is('post')) {
            if (! $this->validate('register')) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors())
                    ->with('warning', $this->msg->validationFailed);
            }

            $email    = (string) $this->request->getPost('email');
            $username = (string) $this->request->getPost('username');
            $password = (string) $this->request->getPost('password');

            // The identity column is `email`, so we register with the email.
            // Ion Auth forces username = identity on register, so we set the
            // real username immediately afterwards.
            $user_id = $this->ion_auth->register($email, $password, $email);

            if ($user_id) {
                $this->ion_auth->update($user_id, ['username' => $username]);

                return redirect()->to(site_url('auth/login'))
                    ->with('success', $this->msg->registerSuccess);
            }

            $ion_error = strip_tags($this->ion_auth->errors());

            return redirect()->back()->withInput()
                ->with('error', $ion_error ?: $this->msg->registerFailed);
        }

        return view('auth/register', [
            'title'  => 'Create account',
            'crumbs' => [
                ['label' => 'Home',     'url' => site_url('/')],
                ['label' => 'Register', 'url' => null],
            ],
        ]);
    }

    // =========================================================================
    // 3) FORGOTTEN PASSWORD
    // =========================================================================

    /**
     * GET: show the "request reset link" form. POST: send the link.
     *
     * The response is always identical so attackers can't probe which accounts
     * exist.
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function forgot_password()
    {
        if ($this->request->is('post')) {
            $login    = (string) $this->request->getPost('login');
            $identity = (new IdentityResolver())->to_identity($login);

            if ($identity !== null) {
                $this->ion_auth->forgottenPassword($identity);
            }

            return redirect()->to(site_url('auth/login'))
                ->with('success', $this->msg->resetSent);
        }

        return view('auth/forgot', [
            'title'  => 'Forgot password',
            'crumbs' => [
                ['label' => 'Home',            'url' => site_url('/')],
                ['label' => 'Log in',          'url' => site_url('auth/login')],
                ['label' => 'Forgot password', 'url' => null],
            ],
        ]);
    }

    /**
     * GET: show the "choose new password" form. POST: apply the new password.
     *
     * @param string $code The reset code from the emailed link.
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function reset_password(string $code)
    {
        $check = $this->ion_auth->forgottenPasswordCheck($code);

        if (! $check) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', $this->msg->resetInvalidCode);
        }

        if ($this->request->is('post')) {
            if (! $this->validate('reset_password')) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors())
                    ->with('warning', $this->msg->validationFailed);
            }

            $identity     = $check->{config('IonAuth')->identity};
            $new_password = (string) $this->request->getPost('password');

            if ($this->ion_auth->resetPassword($identity, $new_password)) {
                return redirect()->to(site_url('auth/login'))
                    ->with('success', $this->msg->resetSuccess);
            }

            $ion_error = strip_tags($this->ion_auth->errors());

            return redirect()->back()->withInput()
                ->with('error', $ion_error ?: $this->msg->unexpectedError);
        }

        return view('auth/reset', [
            'title'  => 'Reset password',
            'code'   => $code,
            'crumbs' => [
                ['label' => 'Home',           'url' => site_url('/')],
                ['label' => 'Reset password', 'url' => null],
            ],
        ]);
    }
}
