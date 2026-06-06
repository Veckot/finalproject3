<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter
 * ===========
 * Route filter that guards the whole `admin/*` group: the visitor must be
 * logged in AND be an administrator. Anyone else is redirected with a flash
 * message.
 *
 * `before()` / `after()` are defined by FilterInterface and keep those names.
 */
class AdminFilter implements FilterInterface
{
    /**
     * Runs before the controller. Blocks non-admins.
     *
     * @param RequestInterface $request   Current request.
     * @param mixed            $arguments Filter arguments (unused).
     * @return \CodeIgniter\HTTP\RedirectResponse|void Redirect when blocked.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $ion_auth = new \IonAuth\Libraries\IonAuth();
        $msg      = config('Messages');

        // Must be logged in.
        if (! $ion_auth->loggedIn()) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', $msg->loginRequired);
        }

        // Must be an administrator.
        if (! $ion_auth->isAdmin()) {
            return redirect()->to(site_url('/'))
                ->with('error', $msg->adminRequired);
        }
    }

    /**
     * Runs after the controller. Nothing to do here.
     *
     * @param RequestInterface  $request   Current request.
     * @param ResponseInterface $response  Current response.
     * @param mixed             $arguments Filter arguments (unused).
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed.
    }
}
