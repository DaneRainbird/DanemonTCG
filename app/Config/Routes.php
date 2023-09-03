<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Cards-related routes 
$routes->get('/cards', 'Cards::index');
$routes->get('/cards/search', 'Cards::search');
$routes->get('/cards/details/(:any)', 'Cards::details/$1');

// Sets-related routes
$routes->get('/sets', 'Sets::index');

// User-related routes
$routes->get('/login', 'Users::login'); // Shorthand-route for /users/login
$routes->get('/logout', 'Users::logout'); // Shorthand-route for /users/logout
$routes->get('/profile' , 'Users::profile'); // Shorthand-route for /users/profile
$routes->get('/users/profile', 'Users::profile');
$routes->get('/users/login', 'Users::login');
$routes->get('/users/logout', 'Users::logout');
$routes->get('/users/callback', 'Users::callback');

// Collection routes
$routes->post('/collections/addCardToCollection', 'Collections::addToCollection');
$routes->post('/collections/removeCardFromCollection', 'Collections::removeFromCollection');
$routes->post('/collections/createCollection', 'Collections::createCollection');
$routes->get('/collections/view/(:any)', 'Collections::view/$1');
$routes->get('/collections/viewAll', 'Collections::viewAll');

// Other / assorted routes
$routes->get('/about', 'Home::about');
$routes->get('/about/queries', 'Home::queries');