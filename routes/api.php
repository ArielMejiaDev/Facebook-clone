<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function() {

    // Single methods
    Route::get('/auth-user', 'User\\Auth\\AuthUserController');

    // All crud actions
    Route::apiResources([
        '/posts' => 'Post\\PostController',
        '/posts/{post}/like' => 'Post\\PostLikeController',
        '/posts/{post}/comment' => 'Post\\PostCommentController',
        '/users' => 'User\\UserController',
        '/users/{user}/posts' => 'User\\Post\\UserPostController',
        '/friend-request' => 'FriendRequest\\FriendRequestController',
        '/friend-request-response' => 'FriendRequest\\FriendRequestResponseController',
        'user-images' => 'Images\\UserImagesController',
    ]);

});
