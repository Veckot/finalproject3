<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();

        if (! $ionAuth->loggedIn()) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'You must log in to access this page.');
        }

        if (! $ionAuth->isAdmin()) {
            return redirect()->to(site_url('/'))
                ->with('error', 'You must be an administrator to access that area.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
