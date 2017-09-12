<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* See: https://laracasts.com/discuss/channels/laravel/makeauth-causes-unable-to-prepare-route-apiuser-for-serialization-uses-closure 
https://laracasts.com/discuss/channels/laravel/why-unable-to-prepare-route-for-serialization-uses-closure
Google: laravel Unable to prepare route [api/user] for serialization. Uses Closure.
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
