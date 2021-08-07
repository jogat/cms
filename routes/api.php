<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** @var \Illuminate\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$router->group(['prefix' => 'session'], function() use ($router) {
    $router->post('login',   'Session@login');
    $router->post('logout',   'Session@logout');
    $router->get('data',   'Session@data');
});

$router->group(['middleware' => 'authRequired'], function() use ($router) {
    $router->get('test','Session@data');

    $router->group(['prefix' => 'post'], function() use ($router) {
        $router->get('',   'Post@index');
        $router->post('',   'Post@store');
        $router->get('{post}',   'Post@show');
        $router->post('{post}',   'Post@update');
        $router->delete('{post}',   'Post@destroy');

        $router->group(['prefix' => '{post}/comment'], function() use ($router) {

            $router->get('',   'Post\Comment@index');
            $router->post('',   'Post\Comment@store');
            $router->get('{comment}',   'Post\Comment@show');
            $router->post('{comment}',   'Post\Comment@update');
            $router->delete('{comment}',   'Post\Comment@archive');
        });

    });
});




