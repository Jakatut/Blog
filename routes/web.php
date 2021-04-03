<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\AuthController;

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


$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');

    $router->group(['prefix' => 'blog'], function () use ($router) {

        // Create a blog.
        $router->post('/', 'BlogPostController@create');

        // Get a list of all blogs
        $router->get('/', 'BlogPostController@find');

        // Get a blog by id.
        $router->get('{id}', 'BlogPostController@get');

        // Update a blog by id.
        $router->put('{id}', 'BlogPostController@update');

        // Delete a blog by id.
        $router->delete('{id}', 'BlogPostController@delete');
    });
});
