<?php namespace App\Http\Middleware;

use Closure;

use App\Configuration as Configuration;
use App\Company as Company;
use App\Context as Context;
use App\Language as Language;
use Illuminate\Support\Str as Str;
use Auth;
use App\User as User;
use Config, App;
use Request, Cookie;
// use \Illuminate\Support\Str;

class SetContextMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		/*
		|--------------------------------------------------------------------------
		| Application Configuration
		|--------------------------------------------------------------------------
		|
		| Load Configuration Keys.
		|
		*/

		// if ( Auth::check() )
		// See: https://laracasts.com/discuss/channels/laravel/how-can-i-access-the-authuser-in-a-middleware-class?page=1
		// if (Auth::guard($guard)->check()) 
		if ( Auth::check() )
		{
			$user = User::with('language')->find( Auth::id() );		// $email = Auth::user()->email;
			$language = $user->language;
			abi_r($language, true);
		} else {
			$user = NULL;
			// https://stackoverflow.com/questions/40846244/get-a-cookie-in-laravel-5-middleware
			$language = Language::find( intval( \Crypt::decrypt(Cookie::get('user_language')) ) );
			
			if ( !$language )
				$language = Language::findOrFail( intval(Configuration::get('DEF_LANGUAGE')) );
			// else fallback to fallback language: in the config/app.php configuration file: 'fallback_locale' => 'en'
			// fallback language should not be deleted
			// \Config::get('app.fallback_locale');
			if ( !$language )
				$language = Language::where('iso_code', '=', \Config::get('app.fallback_locale'))->first();
		}
		// echo \Config::get('app.fallback_locale'); die();
		// echo_r( Language::where('iso_code', '=', \Config::get('app.fallback_locale'))->first() ); die();

		$company = Company::with('currency')->findOrFail( intval(Configuration::get('DEF_COMPANY')) );

		Context::getContext()->user       = $user;
		Context::getContext()->language   = $language;

		Context::getContext()->company    = $company;
		Context::getContext()->currency   = $company->currency;

		// Not really "the controller", but enough to retrieve translation files
		Context::getContext()->controller = $request->segment(1);
		if ($request->segment(3) == 'options' ) Context::getContext()->controller = $request->segment(3);
		if ($request->segment(3) == 'taxrules') Context::getContext()->controller = $request->segment(3);
		Context::getContext()->action     = NULL; //$action; 

/*		
        $action = app('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);

        $routeArray = app('request')->route()->getAction();

        $controllerAction = class_basename($routeArray['controller']);

        list($controller, $action) = explode('@', $controllerAction);
*/



/* * /
		// echo_r($request->route()->getAction());
		// http://laravel.io/forum/10-15-2014-laravel-5-passing-parameters-to-middleware-handlers
		// http://www.codeheaps.com/php-programming/laravel-5-middleware-stack-decoded/
		// http://blog.elliothesp.co.uk/coding/passing-parameters-middleware-laravel-5/
		// https://gist.github.com/dwightwatson/6200599
		// http://stackoverflow.com/questions/26840278/laravel-5-how-to-get-route-action-name
		    $action = $request->route()->getAction();
		    $routeArray = Str::parseCallback($action['controller'], null);

		    if (last($routeArray) != null) {
		        // Remove 'controller' from the controller name.
		        $controller = str_replace('Controller', '', class_basename(head($routeArray)));

		        // Take out the method from the action.
		        $action = str_replace(['get', 'post', 'patch', 'put', 'delete'], '', last($routeArray));

		        // return Str::slug($controller . '-' . $action);
		    } else {
		        // return 'closure';
		        $controller = 'closure';
		        $action = '';
		    }
		// gist ENDS

		Context::getContext()->controller = $controller;
		Context::getContext()->action     = $action; 
		echo Str::slug($controller . '-' . $action);
/ * */

		// Changing Timezone At Runtime. But this change does not seem to be taken by Carbon... Why?
		Config::set('app.timezone', Configuration::get('TIMEZONE'));

		// Changing The Default Language At Runtime
		App::setLocale(Context::getContext()->language->iso_code); 

		// Changing The Default Theme At Runtime
		// https://laracasts.com/discuss/channels/laravel/overriding-laravels-view-with-views-from-custom-package
		$paths = \Config::get('view.paths');
		array_unshift($paths, realpath(base_path('resources/views')).'/../theme');
		\Config::set('view.paths', $paths);

//		print_r(\Config::get('view.paths')); die();

		// https://stackoverflow.com/questions/27458439/how-to-set-view-file-path-in-laravel
		// Apparently setting the config won't change anything because it is loaded when the application bootstraps and ignored afterwards.
		// To change the path at runtime you have to create a new instance of the FileViewFinder. Here's how that looks like:
		$finder = new \Illuminate\View\FileViewFinder(app()['files'], $paths);
		\View::setFinder($finder);


		return $next($request);
	}

}
