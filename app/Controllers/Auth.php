<?php

namespace App\Controllers;

use Config\Messages;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    /** @var \IonAuth\Libraries\IonAuth */
    protected $ionAuth;

    /** @var Messages */
    protected $msg;

    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->msg     = config('Messages');
    }

    public function login()
    {
        if ($this->ionAuth->loggedIn()) {
            return redirect()->to(site_url('/'));
        }

        if ($this->request->is('post')) {
            $identity = (string) $this->request->getPost('identity');
            $password = (string) $this->request->getPost('password');
            $remember = (bool)   $this->request->getPost('remember');

            if ($this->ionAuth->login($identity, $password, $remember)) {
                return redirect()->to(site_url('/'))
                    ->with('success', $this->msg->loginSuccess);
            }

            $ionError = strip_tags($this->ionAuth->errors());

            return redirect()->back()->withInput()
                ->with('error', $ionError ?: $this->msg->loginFailed);
        }

        return view('auth/login', ['title' => 'Log in']);
    }

    public function logout()
    {
        $this->ionAuth->logout();
        return redirect()->to(site_url('/'))
            ->with('success', $this->msg->logoutSuccess);
    }
}
