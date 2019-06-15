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

Route::resource('questions', 'QuestionController')->except('show');

Route::get('/questions/{slug}', 'QuestionController@show')->name('questions.show');

Route::resource('questions.answers', 'AnswerController')->only(['store', 'edit', 'update', 'destroy']);
// or 
// Route::resource('questions.answers', 'AnswerController')->except(['index', 'create', 'show']);

Route::post('/answers/{answer}/accept', 'AcceptAnswerController')->name('answers.accept'); // single action controller

Route::post('/questions/{question}/favorites', 'FavoritesController@store')->name('questions.favorite');
Route::delete('/questions/{question}/favorites', 'FavoritesController@destroy')->name('questions.unfavorite');

Route::post('/questions/{question}/vote', 'VoteQuestionController');
