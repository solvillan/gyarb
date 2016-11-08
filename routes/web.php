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
    $app->get('/{token}/list/friends', 'UserController@listFriends');
});

$app->group(['prefix' => 'game', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    /*
     * Game related POST
     */
    $app->post('/create', 'GameController@create');
    $app->post('/{id}/players/add', 'GameController@addPlayer');
    $app->post('/{id}/start', 'GameController@start');
    $app->post('/{id}/submit/picture', 'GameController@submitPicture');

    /*
     * Game related GET
     */
});