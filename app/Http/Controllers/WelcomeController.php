<?php namespace App\Http\Controllers;

use Request, Cookie;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$languages = \App\Language::orderBy('name')->get();

		// ToDo: remember language using cookie :: echo Request::cookie('user_language');

		return view('welcome')->with(compact('languages'));
	}

	/**
	 * Update DEFAULT language (application wide, not logged-in usersS).
	 *
	 * @return Response
	 */
	public function setLanguage($id)
	{
		$language = \App\Language::findOrFail( $id );

		Cookie::queue('user_language', $language->id, 30*24*60);
		
		return redirect('/');
	}

}
