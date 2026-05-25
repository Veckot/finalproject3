<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Messages;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $msg     = config('Messages');

        if (! $ionAuth->loggedIn()) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', $msg->loginRequired);
        }

        if (! $ionAuth->isAdmin()) {
            return redirect()->to(site_url('/'))
                ->with('error', $msg->adminRequired);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
