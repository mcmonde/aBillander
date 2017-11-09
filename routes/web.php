<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', 'WelcomeController@index');

Route::get('/', 'WelcomeController@index');
Route::get('language/{id}', 'WelcomeController@setLanguage');

Route::get('404', function()
{
    return view('errors.404');
});


// Auth::routes();
/* */
// Route::group(['middleware' => ['web']], function() {

// Login Routes...
    Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
    Route::post('login', ['uses' => 'Auth\LoginController@login']);
    Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

// Registration Routes...
    Route::get('register', ['as' => 'register', 'uses' => 'Auth\RegisterController@showRegistrationForm']);
    Route::post('register', ['uses' => 'Auth\RegisterController@register']);

// Password Reset Routes...
    Route::post('password/email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
    Route::get('password/reset', ['as' => 'password.request', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
    Route::post('password/reset', ['uses' => 'Auth\ResetPasswordController@reset']);
    Route::get('password/reset/{token}', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);

// });
/* */



/* ********************************************************** */


// Secure-Routes
Route::group(['middleware' =>  ['context', 'auth']], function()
{
    // Route::get( 'contact', 'ContactMessagesController@create');
    Route::post('contact', 'ContactMessagesController@store');

    Route::get('soon', function()
    {
        return view('soon');
    });


    // See: https://gist.github.com/drawmyattention/8cb599ee5dc0af5f4246
    Route::group(['middleware' => 'authAdmin'], function()
    {
        // Admin Home Page
        Route::get('/home', 'HomeController@index')->name('home');

        Route::resource('companies', 'CompaniesController');
        
        Route::resource('configurations',    'ConfigurationsController');
        Route::resource('configurationkeys', 'ConfigurationKeysController');

        Route::resource('countries',        'CountriesController');
        Route::resource('countries.states', 'StatesController');
        Route::get('countries/{countryId}/getstates',   array('uses'=>'CountriesController@getStates', 
                                                                'as' => 'countries.getstates' ) );

        Route::resource('languages', 'LanguagesController');

        Route::resource('translations', 'TranslationsController', 
                        ['only' => ['index', 'edit', 'update']]);

        Route::resource('sequences', 'SequencesController');

        Route::resource('users', 'UsersController');

        Route::resource('templates', 'TemplatesController');

// If Stock Counting is in progress, disable these routes:
// if ( ! \App\Configuration::get('STOCK_COUNT_IN_PROGRESS') ) {

        Route::resource('currencies', 'CurrenciesController');
        Route::get('currencies/{id}/exchange',   array('uses'=>'CurrenciesController@exchange', 
                                                                'as' => 'currencies.exchange' ) );  
        Route::post('currencies/ajax/rate_lookup', array('uses' => 'CurrenciesController@ajaxCurrencyRateSearch', 
                                                        'as' => 'currencies.ajax.rateLookup'));   
// }

        Route::resource('taxes',          'TaxesController');
        Route::resource('taxes.taxrules', 'TaxRulesController');

        Route::resource('categories', 'CategoriesController');
        Route::resource('categories.subcategories', 'CategoriesController');
        Route::post('categories/{id}/publish', array('uses' => 'CategoriesController@publish', 
                                                        'as'   => 'categories.publish' ));

        Route::resource('products', 'ProductsController');
        Route::resource('products.images', 'ProductImagesController');

        Route::post('products/{id}/combine', array('as' => 'products.combine', 'uses'=>'ProductsController@combine'));
        Route::get('products/ajax/name_lookup'  , array('uses' => 'ProductsController@ajaxProductSearch', 
                                                        'as'   => 'products.ajax.nameLookup' ));
        Route::post('products/ajax/options_lookup'  , array('uses' => 'ProductsController@ajaxProductOptionsSearch', 
                                                        'as'   => 'products.ajax.optionsLookup' ));
        Route::post('products/ajax/combination_lookup'  , array('uses' => 'ProductsController@ajaxProductCombinationSearch', 
                                                        'as'   => 'products.ajax.combinationLookup' ));
        Route::post('products/ajax/price_lookup', array('uses' => 'ProductsController@ajaxProductPriceSearch', 
                                                        'as' => 'products.ajax.priceLookup'));

        Route::resource('pricelists',     'PriceListsController');
        Route::resource('pricelistlines', 'PriceListLinesController');

        Route::resource('stockcounts',                 'StockCountsController');
        Route::resource('stockcounts.stockcountlines', 'StockCountLinesController');

        Route::resource('optiongroups',         'OptionGroupsController');
        Route::resource('optiongroups.options', 'OptionsController');

        Route::resource('combinations', 'CombinationsController');

        Route::resource('images', 'ImagesController');

        Route::resource('warehouses', 'WarehousesController');

        Route::resource('stockmovements', 'StockMovementsController');

        Route::resource('stockadjustments', 'StockAdjustmentsController', 
                ['except' => [
                    'index', 'update', 'destroy'
                ]]);

        Route::resource('customers', 'CustomersController');
        Route::get('customers/ajax/name_lookup', array('uses' => 'CustomersController@ajaxCustomerSearch', 'as' => 'customers.ajax.nameLookup'));

//        Route::resource('addresses', 'AddressesController');
        Route::resource('customers.addresses', 'CustomerAddressesController');

        Route::post('mail', 'MailController@store');

        Route::resource('paymentmethods', 'PaymentMethodsController');

        Route::resource('customergroups', 'CustomerGroupsController');
        
        Route::resource('salesreps', 'SalesRepsController');

        Route::resource('carriers', 'CarriersController');

        Route::resource('manufacturers', 'ManufacturersController');


        Route::resource('customerinvoices'      , 'CustomerInvoicesController');
        Route::get('customerinvoices/{id}/pdf'  , 'CustomerInvoicesController@ShowPDF');
        Route::post('customerinvoices/sendemail', 'CustomerInvoicesController@SendEmail');
        Route::post('customerinvoices/ajax/price_lookup', array('uses' => 'CustomerInvoicesController@ajaxLineSearch', 
                                                        'as' => 'customerinvoices.ajax.lineLookup'));
        Route::post('customerinvoices/ajax/other_lookup', array('uses' => 'CustomerInvoicesController@ajaxLineOtherSearch', 
                                                        'as' => 'customerinvoices.ajax.lineOtherLookup'));


        Route::resource('customervouchers'      , 'CustomerVouchersController');

        Route::get('pdf/{id}', 'PdfController@show');

//        Route::resource('images', 'ImagesController');
    });


});

/* ********************************************************** */


Route::group(['prefix' => 'abcc', 'namespace' => '\CustomerCenter'], function ()
{
//    Route::get('/', ['uses' => 'DashboardController@redirectToLogin']);
    Route::get('/', ['uses' => 'PublicCustomerInvoicesController@index']);
    Route::get('invoice/{invoiceKey}', ['uses' => 'PublicCustomerInvoicesController@show', 'as' => 'customerCenter.public.invoice.show']);
    Route::get('invoice/{invoiceKey}/pdf', ['uses' => 'PublicCustomerInvoicesController@pdf', 'as' => 'customerCenter.public.invoice.pdf']);

/*
    Route::group(['middleware' => 'auth.customerCenter'], function ()
    {
        Route::get('dashboard', ['uses' => 'CustomerCenterDashboardController@index', 'as' => 'customerCenter.dashboard']);
        Route::get('invoices' , ['uses' => 'CustomerCenterInvoiceController@index'  , 'as' => 'customerCenter.invoices']);
        Route::get('quotes'   , ['uses' => 'CustomerCenterQuoteController@index'    , 'as' => 'customerCenter.quotes']);
        Route::get('payments' , ['uses' => 'CustomerCenterPaymentController@index'  , 'as' => 'customerCenter.payments']);
    });
*/
});

/* ********************************************************** */


if (file_exists(__DIR__.'/gorrino_routes.php')) {
    include __DIR__.'/gorrino_routes.php';
}

/* ********************************************************** */


/* ********************************************************** */

