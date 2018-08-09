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
    return $router->app->version();
});

$router->post('/auth', 'AuthController@auth');
$router->get('/auth/check', 'AuthController@check');
$router->post('/auth/refresh', 'AuthController@check');

$router->group(['middleware'=>['authorize']], function() use($router){
    $router->post('/reg', 'UsersController@register');
});