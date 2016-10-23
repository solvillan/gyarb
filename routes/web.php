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
    return view("index");
});
$app->get('/register', function () use ($app) {
   return view("register");
});
$app->get('/download', function () use ($app) {
    return view("download");
});

$app->post('/create', 'UserController@create');
$app->post('/user/auth', 'UserController@auth');
$app->post('/check-token', 'UserController@checkToken');

