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

/*
 * The webpage routes
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

/*
 * User related POST
 */
$app->post('/user/create', 'UserController@create');
$app->post('/user/auth', 'UserController@auth');
$app->post('/check-token', 'UserController@checkToken');

/*
 * User related GET
 */
$app->get('/user/list', 'UserController@listUsers');
$app->get('/user/list/filter/{filter}', 'UserController@listUsersFiltered');
$app->get('/user/{token}/list/friends', 'UserController@listFriends');

/*
 * Game related POST
 */
$app->post('/game/create', 'GameController@create');
$app->post('/game/{id}/players/add', 'GameController@addPlayer');

/*
 * Game related GET
 */