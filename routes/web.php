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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('getKey', function () use ($router) {
    return str_random('32');
});

$router->group(['prefix' => 'api/v1/', 'middleware' => 'CORS'], function () use ($router) {

    $router->group(['prefix' => 'users', 'middleware' => ['JWTAuth',"Role:Super Admin,Admin"] ], function () use ($router) {
       
        // Show all users
        $router->get('/', ['uses'=>"UserController@index"]);
        // Show selected user
        $router->get('/{id}', ['uses'=>"UserController@show"]);
        // Update data user
        $router->put('/{id}', ['uses'=>"UserController@update"]);
        // Delete data user
        $router->delete('/{id}', ['uses'=>"UserController@destroy"]);

    });
    
    $router->group(['prefix' => 'auth'], function () use ($router) {

        // Create a new user
        $router->post('signup', ['uses'=>"UserController@create", 'middleware' => ['throttle:1,1'] ]);
        // Sign In
        $router->post('signin', ['uses'=>"UserController@authenticate", 'middleware' => ['throttle:3,1']]);
    
    });

});
