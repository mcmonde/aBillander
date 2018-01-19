<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

use App\CustomerUser as CustomerUser;

class CustomerLoginController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest')->except('logout');
        $this->middleware('guest:customer')->except('logout');
    }

    public function showLoginForm()
    {
      $languages = \App\Language::orderBy('name')->get();

      // ToDo: remember language using cookie :: echo Request::cookie('user_language');

      return view('auth.customer_login')->with(compact('languages'));
    }

    public function login(Request $request)
    {
      // Validate the form data
      $this->validate($request, CustomerUser::$rules);

      // Attempt to log the user in
      if (Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
        
        // if successful, then redirect to their intended location
        return redirect()->intended(route('customer.dashboard'));
      }

      // if unsuccessful, then redirect back to the login with the form data
      return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    // See: /vendor/laravel/framework/src/Illuminate/Foundation/Auth/AuthenticatesUsers.php
    // No customization: logout ALL users at once!
/*    
    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/');
    }
*/

    /**
     * Update DEFAULT language (application wide, not logged-in usersS).
     *
     * @return Response
     */
    public function setLanguage($id)
    {
      $language = \App\Language::findOrFail( $id );

      Cookie::queue('user_language', $language->id, 30*24*60);
      
      return redirect('/abcc');
    }
}
