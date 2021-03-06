<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

// Disable registration routes
Route::get('/register', function() {
    return "";
});
Route::post('/register', function() {
    return "";
});


Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    // get routes
    Route::get('/dashboard', 'HomeController@index');
    Route::get('/post/{id}', 'PublicController@getPost');
    Route::get('/posts', 'PublicController@index');
    Route::get('/posts/create', 'PostsController@create');
    Route::get('/post/{id}/edit', 'PostsController@editPost');
    Route::get('/feed', 'PublicController@feed');

    // post routes
    Route::post('/user/changeName', 'HomeController@changeNamePostback');
    Route::post('/user/changeEmail', 'HomeController@changeEmailPostback');
    Route::post('/user/changePassword', 'HomeController@changePasswordPostback');
    Route::post('/posts/approveComment', 'PostsController@approveCommentPostback');
    Route::post('/posts/unapproveComment', 'PostsController@unapproveCommentPostback');
    Route::post('/posts/deleteComment', 'PostsController@deleteCommentPostback');
    Route::post('/posts/hide', 'PostsController@hidePostback');
    Route::post('/posts/show', 'PostsController@showPostback');
    Route::post('/posts/delete', 'PostsController@deletePostback');
    Route::post('/posts/create', 'PostsController@createPostback');
    Route::post('/posts/update', 'PostsController@updatePostback');
    Route::post('/posts/updatePagination', 'PostsController@updatePagination');
    Route::post('/posts/createComment', 'PublicController@createCommentPostback');


});
