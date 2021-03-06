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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'HomeController@index');

Route::get('/test', function(){
    return view('test');
});

Route::get('/download/{name}', 'HomeController@download');

Route::get('/play/{name}', 'HomeController@play');

Route::get('/report_ob', 'HomeController@report_ob');
Route::get('/report_ob_get', 'HomeController@report_ob_get');
//Route::post('/report_ob_post', 'HomeController@report_ob_post');
Route::post('/report_ob_post', 'HomeController@report_ob_post');
Route::post('/report_ob_post2', 'TestController@report_ob_post2');    // test

Route::get('/not_authorize', function(){
    echo "user is not authorize is admin";
});

Route::get('/not_active', function(){
    echo "User is not active. Please contact Admin to active.";
});

Auth::routes();

Route::get('/home', 'HomeController@home')->name('home');
