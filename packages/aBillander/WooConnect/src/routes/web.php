<?php

// github.com/gocanto/gocanyo-pkg


Route::group([

	'namespace' => 'aBillander\WooConnect\Http',
	'prefix'    => 'wooc'

], function () {

	Route::get('/', 'WooConnectController@hello');

});


Route::group([

	'middleware' =>  ['web', 'context', 'auth', 'authAdmin'],
	'namespace' => 'aBillander\WooConnect\Http\Controllers',
	'prefix'    => 'wooc'

], function () {

	Route::get('orders', ['as' => 'worders', 'uses' => 'WooOrdersController@index']);
	Route::get('orders/statuses', 'WooOrdersController@getStatuses');
	Route::get('orders/{id}', 'WooOrdersController@show');
	Route::post('orders/{id}', ['as' => 'wostatus', 'uses' => 'WooOrdersController@update']);

});

