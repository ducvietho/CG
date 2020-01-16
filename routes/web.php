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
    $router->post('/request/delete','CareController@delete');

    /**
     * Get list location city, district
     */
    $router->group(['prefix' => 'location'], function () use ($router) {
        $router->post('/getCity', 'LocationController@getListCity');
        $router->post('/getDistrict', 'LocationController@getListDistrict');
    });

    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('register','RegisterController@register');
        $router->post('checkID','RegisterController@checkID');
        $router->post('registerSocial','RegisterController@registerSocial');
        $router->post('login','Login1Controller@login');
        $router->post('loginSocial','Login1Controller@loginSocial');
        $router->post('findID','UserController@findID');
        $router->post('forgotPass','UserController@forgotPass');
    });


    $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
        $router->group(['prefix' => 'user'], function () use ($router) {
            $router->post('/changePass','UserController@changePass');
            $router->post('/logout','UserController@logout');
            $router->post('/editProfile','UserController@editProfile');
            $router->post('/settingCare','UserController@settingCare');
            $router->post('/notification','UserController@getNoti');
            $router->post('/destroy','UserController@cancelAccount');
            $router->post('/requested','UserController@getListRequested');

        });
        $router->group(['prefix' => 'nurse'], function () use ($router) {
            $router->post('/home', 'NurseController@homePatient');
            $router->post('/register', 'NurseController@registerNurse');
            $router->post('/update', 'NurseController@updateProfile');
            $router->post('/interest', 'NurseController@interest');
            $router->post('/suggest', 'NurseController@suggest');
            $router->post('/manager', 'NurseController@manager');
            $router->post('/detail', 'NurseController@detail');
            $router->post('search','NurseController@searchPatient');
            $router->post('/nureInterestAction', 'NurseController@nureInterestAction');
            $router->post('request','CareController@requestCare');
            $router->post('accept','CareController@nurseAcept');
            $router->post('complete','CareController@nurseComplete');
        });
        $router->group(['prefix' => 'patient'], function () use ($router) {
            $router->post('/create', 'PatientController@create');
            $router->post('/delete', 'PatientController@delete');
            $router->post('/getList', 'PatientController@getList');
            $router->post('/home', 'PatientController@homePatient');
            $router->post('/interest/list', 'PatientController@interest');
            $router->post('/interest/action', 'PatientController@interestAction');
            $router->post('/detail', 'PatientController@detail');
            $router->post('/suggest', 'PatientController@suggest');
            $router->post('/update', 'PatientController@update');
            $router->post('/manager', 'PatientController@manage');
            $router->post('/search', 'NurseController@searchNurse');
            $router->post('request','CareController@patientRequest');
            $router->post('accept','CareController@patientAcept');
            $router->post('rate','CareController@patientRating');
        });
        $router->group(['prefix' => 'certificate'], function () use ($router) {
            $router->post('sign','CertificateController@signCertificate');
            $router->post('get','CertificateController@getCertificate');
        });
        $router->group(['middleware' => 'admin'], function () use ($router) {
            $router->group(['prefix' => 'admin'], function () use ($router) {
                $router->post('loginLogs','CMS\UserController@getLoginLogs');
                $router->post('getUsers','CMS\UserController@getListUser');
                $router->post('getPatients','CMS\PatientController@getPatients');
                $router->post('getNurses','CMS\NurseController@getNurses');
                $router->post('getRequests','CMS\CareController@getRequests');
            });
        });

        $router->post('care/detail','CareController@detail');
    });
});
