<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return 'running';
});

// Create a blog.
$router->post('blog', 'BlogPostController@create');

// Get a list of all blogs
$router->get('blog', 'BlogPostController@find');

// Get a blog by id.
$router->get('blog/{id}', 'BlogPostController@get');

// Update a blog by id.
$router->put('blog/{id}', 'BlogPostController@update');

// Delete a blog by id.
$router->delete('blog/{id}', 'BlogPostController@delete');
