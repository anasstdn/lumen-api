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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->group(['prefix'=>'api/v1'], function() use($router){
    $router->post('/login', 'AuthController@login');
});

$router->group(['prefix'=>'api/v1','middleware' => 'auth:api'], function($router){
    $router->post('/logout',[
        'uses' => 'AuthController@logout'
    ]);
});

$router->group(['prefix'=>'api/v1','middleware' => 'auth:api'], function() use($router){
    // $router->get('/transaksi',['middleware' => 'auth', 'uses' => 'TransaksiController@index']);
    $router->post('/transaksi','TransaksiController@index');
    $router->post('/transaksi/create', 'TransaksiController@create');
    $router->get('/transaksi/{id}', 'TransaksiController@show');
    $router->put('/transaksi/{id}', 'TransaksiController@update');
    $router->delete('/transaksi/{id}', 'TransaksiController@destroy');
});

$router->group(['prefix'=>'api/v1','middleware' => 'auth:api'], function() use($router){
    // $router->get('/transaksi',['middleware' => 'auth', 'uses' => 'TransaksiController@index']);
    $router->post('/peramalan','PeramalanController@index');
    $router->post('/peramalan/create', 'PeramalanController@create');
    $router->get('/peramalan/{id}', 'PeramalanController@show');
    $router->put('/peramalan/{id}', 'PeramalanController@update');
    $router->delete('/peramalan/{id}', 'PeramalanController@destroy');
});

$router->group(['prefix'=>'api/v1','middleware' => 'auth:api'], function() use($router){
    // $router->get('/transaksi',['middleware' => 'auth', 'uses' => 'TransaksiController@index']);
    $router->post('/agama/get-data','AgamaController@getData');
    $router->post('/agama/create', 'AgamaController@create');
    $router->get('/agama/{id}/edit', 'AgamaController@edit');
    $router->put('/agama/{id}/update', 'AgamaController@update');
    $router->delete('/agama/{id}', 'AgamaController@destroy');
});

$router->group(['prefix'=>'api/v1','middleware' => 'auth:api'], function() use($router){
    // $router->get('/transaksi',['middleware' => 'auth', 'uses' => 'TransaksiController@index']);
    $router->post('/jabatan/get-data','JabatanController@getData');
    $router->post('/jabatan/create', 'JabatanController@create');
    $router->get('/jabatan/{id}/edit', 'JabatanController@edit');
    $router->put('/jabatan/{id}/update', 'JabatanController@update');
    $router->delete('/jabatan/{id}', 'JabatanController@destroy');
});

$router->group(['prefix'=>'api/v1','middleware' => 'auth:api'], function() use($router){
    // $router->get('/transaksi',['middleware' => 'auth', 'uses' => 'TransaksiController@index']);
    $router->post('/departement/get-data','DepartementController@getData');
    $router->post('/departement/create', 'DepartementController@create');
    $router->get('/departement/{id}/edit', 'DepartementController@edit');
    $router->put('/departement/{id}/update', 'DepartementController@update');
    $router->delete('/departement/{id}', 'DepartementController@destroy');
});