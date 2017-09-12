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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SetContextMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{

		/*
		|--------------------------------------------------------------------------
		| Application Configuration
		|--------------------------------------------------------------------------
		|
		| Load Context.
		|
		*/

		$user = User::with('language')->find( Auth::id() );		// $email = Auth::user()->email;
		$language = $user->language;

		// https://stackoverflow.com/questions/40846244/get-a-cookie-in-laravel-5-middleware
		if ( !$language )
			$language = Language::find( intval( \Crypt::decrypt(Cookie::get('user_language')) ) );
		
		if ( !$language )
			$language = Language::find( intval(Configuration::get('DEF_LANGUAGE')) );

		if ( !$language )
			$language = Language::where('iso_code', '=', \Config::get('app.fallback_locale'))->first();

		if ( !$language ) {
			$language = new stdClass();
			$language->iso_code = \Config::get('app.fallback_locale');
		}
		

		try {
			$company = Company::with('currency')->findOrFail( intval(Configuration::get('DEF_COMPANY')) );
		} catch (ModelNotFoundException $ex) {
			// If Company does not found. Not any good here...
			$company = new stdClass();
			$company->currency = NULL;	// Or fallback to Configuration::get('DEF_CURRENCY')

			// Maybe:
			// abort(404);
			// Or redirect to installer
			// if (\Route::currentRouteName() != 'installer') {
    		//	return redirect()->route('installer');
		}

		Context::getContext()->user       = $user;
		Context::getContext()->language   = $language;

		Cookie::queue('user_language', $language->id, 30*24*60);

		Context::getContext()->company    = $company;
		Context::getContext()->currency   = $company->currency;

		// Not really "the controller", but enough to retrieve translation files
		Context::getContext()->controller = $request->segment(1);
		if ($request->segment(3) == 'options' ) Context::getContext()->controller = $request->segment(3);
		if ($request->segment(3) == 'taxrules') Context::getContext()->controller = $request->segment(3);
		Context::getContext()->action     = NULL;

		// Changing Timezone At Runtime. But this change does not seem to be taken by Carbon... Why?
		Config::set('app.timezone', Configuration::get('TIMEZONE'));

		// Changing The Default Language At Runtime
		App::setLocale(Context::getContext()->language->iso_code); 

/*
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
*/

		return $next($request);
	}

}
