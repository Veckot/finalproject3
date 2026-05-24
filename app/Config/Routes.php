<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/',              'Home::index');
$routes->get('movie/(:num)',   'Home::show/$1');

// Auth (IonAuth)
$routes->group('auth', static function ($routes) {
    $routes->match(['get', 'post'], 'login',  '\App\Controllers\Auth::login');
    $routes->get('logout',                    '\App\Controllers\Auth::logout');
});

// Admin (protected by AdminFilter)
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('/',                                  'Admin::index');
    $routes->get('add',                                'Admin::add');
    $routes->post('store',                             'Admin::store');
    $routes->get('list',                               'Admin::listEntries');
    $routes->get('edit/(:segment)/(:num)',             'Admin::edit/$1/$2');
    $routes->post('update/(:segment)/(:num)',          'Admin::update/$1/$2');
    $routes->post('delete/(:segment)/(:num)',          'Admin::delete/$1/$2');
});
