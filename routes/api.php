<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function() {

    Route::get('/auth-user', 'User\\Auth\\AuthUserController');

    Route::apiResources([
        '/posts' => 'Post\\PostController',
        '/users' => 'User\\UserController',
        '/users/{user}/posts' => 'User\\Post\\UserPostController'
    ]);

});
