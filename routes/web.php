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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->post('/create', 'UserController@create');
$app->post('/user/auth', ['middleware' => 'auth', 'uses' => 'UserController@auth']);

/*$app->group(['middleware' => 'auth'], function () use ($app) {

});*/