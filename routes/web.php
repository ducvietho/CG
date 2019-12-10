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
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/import', 'LocationController@import');
    $router->post('/importDistrict', 'LocationController@importDistrict');

    /**
     * Get list location city, district
     */
    $router->group(['prefix' => 'location'], function () use ($router) {
        $router->post('/getCity', 'LocationController@getListCity');
        $router->post('/getDistrict', 'LocationController@getListDistrict');
    });

    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('register','RegisterController@register');
        $router->post('login','LoginController@login');
    });

    $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
        $router->group(['prefix' => 'nurse'], function () use ($router) {
            $router->post('/home', 'NurseController@homePatient');
        });
    });
});
