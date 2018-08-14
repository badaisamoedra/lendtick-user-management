<?php

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
    // return $router->app->version();
    return redirect('/api/documentation');
});
$router->get('/api', function () use ($router) {
    // return $router->app->version();
    return redirect('/api/documentation');
});

$router->post('/auth', 'AuthController@auth');
$router->get('/auth/check', 'AuthController@check');
$router->get('/auth/refresh', 'AuthController@refresh');
$router->post('/reg', 'UsersController@register');

$router->group(['middleware'=>['authorize']], function() use($router){
});