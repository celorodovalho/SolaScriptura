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
    Route::get('bot', 'Bot\DefaultController@show');
    Route::post('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', 'Bot\CommandHandlerController@webhook');
    Route::get('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', 'Bot\CommandHandlerController@webhook')->name('webhook');
    Route::get('/setWebhook', 'Bot\DefaultController@setWebhook');
    Route::get('/removeWebhook', 'Bot\DefaultController@removeWebhook');
    Route::get('/getUpdates', 'Bot\DefaultController@getUpdates');
    Route::get('/getWebhookInfo', 'Bot\DefaultController@getWebhookInfo');
    Route::get('/getMe', 'Bot\DefaultController@getMe');
    Route::any('/sendMessage', 'Bot\DefaultController@sendMessage');
});