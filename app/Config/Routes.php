<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -----------------------------------------------------------------------------
// Public site (Home + Stats)
// -----------------------------------------------------------------------------
$routes->get('/',              'Home::index');
$routes->get('movie/(:num)',   'Home::show/$1');
$routes->get('people',         'Home::people');
$routes->get('person/(:num)',  'Home::person/$1');
$routes->get('genres',         'Home::genres');
$routes->get('genre/(:num)',   'Home::genre/$1');
$routes->get('stats',          'Stats::index');

// -----------------------------------------------------------------------------
// Authentication (Ion Auth)
// -----------------------------------------------------------------------------
$routes->group('auth', static function ($routes) {
    $routes->match(['get', 'post'], 'login',            'Auth::login');
    $routes->get('logout',                              'Auth::logout');
    $routes->match(['get', 'post'], 'register',         'Auth::register');
    $routes->match(['get', 'post'], 'forgot',           'Auth::forgot_password');
    $routes->match(['get', 'post'], 'reset/(:segment)', 'Auth::reset_password/$1');
});

// -----------------------------------------------------------------------------
// Administration (protected by the `admin` filter)
// -----------------------------------------------------------------------------
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    // Entity CRUD (movie / genre / person)
    $routes->get('/',                                  'Admin::index');
    $routes->get('add',                                'Admin::add');
    $routes->post('store',                             'Admin::store');
    $routes->get('list',                               'Admin::list_entries');
    $routes->get('edit/(:segment)/(:num)',             'Admin::edit/$1/$2');
    $routes->post('update/(:segment)/(:num)',          'Admin::update/$1/$2');
    $routes->post('delete/(:segment)/(:num)',          'Admin::delete/$1/$2');

    // Movie cast & crew management
    $routes->post('movie/(:num)/people',               'Admin::attach_person/$1');
    $routes->post('movie/(:num)/people/(:num)/detach', 'Admin::detach_person/$1/$2');

    // User management
    $routes->get('users',                              'Admin::users');
    $routes->get('users/(:num)/edit',                  'Admin::edit_user/$1');
    $routes->post('users/(:num)',                      'Admin::update_user/$1');
    $routes->post('users/(:num)/delete',               'Admin::delete_user/$1');
});
