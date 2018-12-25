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
Route::post('slack/create', 'HomeController@create')->name('slack.create');
Route::post('slack/invite', 'HomeController@invite')->name('slack.invite');

Route::get('test', 'HomeController@test');

Route::get('conversation/{id}', 'SlackController@startConversation')->name('conversation');

Route::get('get-history/{channel}', 'SlackController@getChannelHistory')->name('history');

Route::get('chat/{id}', 'HomeController@chat')->name('chat');

Route::post('send-message/{id}', 'SlackController@send')->name('send');