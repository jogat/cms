<?php

/** @var \Illuminate\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->view('/spa/{any_path?}', 'client')->where('any_path', '(.*)');
$router->view('/home', 'cms.home')->middleware('authRequired')->name('home');

$router->get('/login', 'Session@login_view')->name('login');
$router->post('/login', 'Session@login');
$router->get('/logout',  'Session@logout');


