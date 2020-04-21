<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function() {

    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });

    // Route::apiResource('/posts', 'Post\\PostController');

    Route::apiResources([
        '/posts' => 'Post\\PostController',
        '/users' => 'User\\UserController',
        '/users/{user}/posts' => 'User\\Post\\UserPostController'
    ]);

});
