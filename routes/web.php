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
    return config('app.name');
});

$router->group(['prefix' => 'api/v1/transactions/', 'namespace' => 'V1'], function () use ($router) {

    // Transfer
    $router->post('transfer', 'TransferController@handle');
});
