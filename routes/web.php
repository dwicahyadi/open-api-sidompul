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
    return "Open API siDOMPUL by Dwi Cahyadi";
});

$router->post('/chip/register', ['as' => 'chip.register', 'uses' => 'ChipController@register']);
$router->post('/chip/addUrl', ['as' => 'chip.addUrl', 'uses' => 'ChipController@addEndpoint']);


$router->post('/trx', ['as' => 'trx', 'uses' => 'TrxController']);
$router->get('/trx', ['as' => 'trx', 'uses' => 'TrxController']);

$router->post('/history', ['as' => 'history', 'uses' => 'HistoryController']);
$router->get('/history', ['as' => 'history', 'uses' => 'HistoryController']);
