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
$app->get('/login', function () use ($app) {
    return view("login");
});
$app->post('/login', 'UserController@authSession');
$app->get('/logout', 'UserController@logout');

$app->group(['prefix' => 'user', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    /*
     * User related POST
     */
    $app->post('/create', 'UserController@create');
    $app->post('/auth', 'UserController@auth');
    $app->post('/check-token', 'UserController@checkToken');

    /*
     * User related GET
     */
    $app->get('/list', 'UserController@listUsers');
    $app->get('/list/filter/{filter}', 'UserController@listUsersFiltered');
    $app->get('/list/friends', 'UserController@listFriends');
    $app->get('/{id}/list/friends', 'UserController@listFriendsForId');
});

$app->group(['prefix' => 'game', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    /*
     * Game related POST
     */
    $app->post('/create', 'GameController@create');
    $app->post('/{gid}/players/add', 'GameController@addPlayer');
    $app->post('/{gid}/start', 'GameController@start');
    $app->post('/{gid}/submit/picture', 'GameController@submitPicture');

    /*
     * Game related GET
     */
    $app->get('/{gid}/poll', 'GameController@poll');
});