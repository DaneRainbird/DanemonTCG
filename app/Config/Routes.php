<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Cards-relatedd routes 
$routes->get('/cards', 'Cards::index');
$routes->get('/cards/search', 'Cards::search');
$routes->get('/cards/details/(:any)', 'Cards::details/$1');

// Sets-related routes
$routes->get('/sets', 'Sets::index');

// User-related routes
$routes->get('/login', 'Users::login'); // Shorthand-route for /users/login
$routes->get('/logout', 'Users::logout'); // Shorthand-route for /users/logout
$routes->get('/users/login', 'Users::login');
$routes->get('/users/logout', 'Users::logout');
$routes->get('/users/callback', 'Users::callback');

// Other / assorted routes
$routes->get('/about', 'Home::about');
$routes->get('/about/queries', 'Home::queries');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
