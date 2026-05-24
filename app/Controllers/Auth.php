<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    /** @var \IonAuth\Libraries\IonAuth */
    protected $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function login()
    {
        if ($this->ionAuth->loggedIn()) {
            return redirect()->to(site_url('/'));
        }

        if ($this->request->is('post')) {
            $identity = (string) $this->request->getPost('identity');
            $password = (string) $this->request->getPost('password');
            $remember = (bool) $this->request->getPost('remember');

            if ($this->ionAuth->login($identity, $password, $remember)) {
                return redirect()->to(site_url('/'))->with('message', 'Welcome back!');
            }

            return redirect()->back()->withInput()
                ->with('error', $this->ionAuth->errors() ?: 'Invalid credentials.');
        }

        return view('auth/login', ['title' => 'Log in']);
    }

    public function logout()
    {
        $this->ionAuth->logout();
        return redirect()->to(site_url('/'))->with('message', 'You have been logged out.');
    }
}
