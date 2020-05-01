<?php

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

Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {
        return view('main.welcome');
    });
    
    Route::get('auth/{provider}', 'Auth\AuthController@redirectToProvider');
    Route::get('auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback');

    Auth::routes();

    //Admin Auth
    Route::get('/security/login',[
        'uses' => 'Auth\LoginController@adminLoginView',
        'as' => 'security.login'
    ]);

    Route::post('/security/login',[
        'uses' => 'Auth\LoginController@authenticateAdmin',
        'as' => 'security.login'
    ]);

    Route::get('/dashboard',[
        'uses' => 'HomeController@index',
        'as' => 'dashboard'
    ]);

    Route::get('/verify/token/{token}', [
        'uses' => 'Auth\VerificationController@verify',
        'as' => 'auth.verify'
    ]); 
 
    Route::get('/verify/resend', [
        'uses' => 'Auth\VerificationController@resend',
        'as' => 'auth.verify.resend'
    ]); 

});


Route::group(['middleware' => ['auth']], function () {

});

Route::group(['middleware' => ['auth','is_admin']], function () {
     
    Route::prefix('security')->group(function () {
        
        Route::get('/dashboard',[
            'uses' => 'AdminController@dashboard',
            'as' => 'security.dashboard'
        ]);

    });    

});
