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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/email/verify/{email}/{token}', 'Email\VerificationController@verify')
    ->name('email.verify');

Route::get('/email/verify-result', 'Email\VerificationController@showVerificationStatus')
    ->name('email.verified');

Route::post('/home/subscribe', 'Email\SubscriptionController@subscribe')
    ->name('user.subscribe');

Route::post('/home/change/email', 'HomeController@handleEmailChange')
    ->name('user.email');

Route::post('/home/verify/send', 'HomeController@verify')
    ->name('user.verify');

Route::post('/home/test/send', 'HomeController@test')
    ->name('user.test.email');

Route::get('/email/unsubscribe/{email}/{token}', 'Email\SubscriptionController@unsubscribe')
    ->name('email.unsubscribe');

Route::get('/email/unsubscribe-result', 'Email\SubscriptionController@showUnsubscribeStatus')
    ->name('email.unsubscribed');
