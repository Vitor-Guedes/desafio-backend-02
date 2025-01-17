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

$router->post('api/currency', [
    'as' => 'api.currency.store',
    'uses' => 'CurrencyController@store'
]);

$router->delete('api/currency/{code}', [
    'as' => 'api.currency.destroy',
    'uses' => 'CurrencyController@destroy'
]);

$router->get('api/currency/{code}', [
    'as' => 'api.currency.quote',
    'uses' => 'CurrencyController@quote'
]);