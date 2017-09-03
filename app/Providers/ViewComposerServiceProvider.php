<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

/*
|--------------------------------------------------------------------------
| Application View Composers
|--------------------------------------------------------------------------
|
| Load View Composers.
|
*/

class ViewComposerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Currencies
		view()->composer(array('customers.edit', 'customer_invoices.create', 'customer_invoices.edit', 'companies.edit', 'customer_groups.create', 'customer_groups.edit'), function($view) {
		    
		    $view->with('currencyList', \App\Currency::lists('name', 'id'));
		    
		});

		// Customer Groups
		view()->composer(array('customers.edit'), function($view) {
		    
		    $view->with('customer_groupList', \App\CustomerGroup::lists('name', 'id'));
		    
		});

		// Payment Methods
		view()->composer(array('customers.edit', 'customer_invoices.create', 'customer_invoices.edit', 'customer_groups.create', 'customer_groups.edit'), function($view) {
		    
		    $view->with('payment_methodList', \App\PaymentMethod::lists('name', 'id'));
		    
		});

		// Sequences
		view()->composer(array('customers.edit', 'customer_invoices.create', 'customer_invoices.edit', 'customer_groups.create', 'customer_groups.edit'), function($view) {
		    
		    $view->with('sequenceList', \App\Sequence::lists('name', 'id'));
		    
		});

		// Invoice Template
		view()->composer(array('customers.edit', 'customer_invoices.create', 'customer_invoices.edit', 'customer_groups.create', 'customer_groups.edit'), function($view) {
		    
		    $view->with('customerinvoicetemplateList', \App\Template::where('model_name', '=', 'CustomerInvoice')->lists('name', 'id'));
		    
		});

		// Carriers
		view()->composer(array('customers.edit', 'customer_invoices.create', 'customer_invoices.edit', 'customer_groups.create', 'customer_groups.edit'), function($view) {
		    
		    $view->with('carrierList', \App\Carrier::lists('name', 'id'));
		    
		});

		// Sales Representatives
		view()->composer(array('customers.edit', 'customer_invoices.create', 'customer_invoices.edit'), function($view) {
		    
		    $view->with('salesrepList', \App\SalesRep::select(DB::raw('alias as name, id'))->lists('name', 'id'));
		    
		});

		// Price Lists
		view()->composer(array('customers.edit', 'customer_groups.create', 'customer_groups.edit'), function($view) {
		    
		    $view->with('price_listList', \App\PriceList::lists('name', 'id'));
		    
		});

		// Warehouses
		view()->composer(array('products.create', 'stock_movements.create', 'stock_adjustments.create', 'configuration_keys.key_group_2', 'customer_invoices.create', 'customer_invoices.edit'), function($view) {
		    
		    $whList = \App\Warehouse::with('address')->get();

		    $list = [];
		    foreach ($whList as $wh) {
		    	$list[$wh->id] = $wh->address->alias;
		    }

		    $view->with('warehouseList', $list);
		    // $view->with('warehouseList', \App\Warehouse::lists('name', 'id'));
		    
		});

		// Taxes
		view()->composer(array('customer_invoices.create', 'customer_invoices.edit', 'products.create', 'products.edit'), function($view) {
		    
		    $view->with('taxList', \App\Tax::orderby('percent', 'desc')->lists('name', 'id'));
		    
		});

		view()->composer(array('products.create', 'products.edit', 'prices.edit', 'customer_invoices.create', 'customer_invoices.edit'), function($view) {

		    $view->with('taxpercentList', \App\Tax::lists('percent', 'id'));
		    
		});

		view()->composer(array('customer_invoices.create', 'customer_invoices.edit'), function($view) {

		    $list = \App\Tax::select(
//		        \DB::raw("(percent + extra_percent) AS percent, id")
		        \DB::raw("(percent) AS percent, id")
		    )->lists('percent', 'id');

		    $view->with('taxeqpercentList', $list);
		    
		});

		// Languages
		view()->composer(array('users.create', 'users.edit'), function($view) {
		    
		    $view->with('languageList', \App\Language::lists('name', 'id'));
		    
		});

		// Categories
		view()->composer(array('products.index', 'products.create', 'products._panel_main_data'), function($view) {
		    
		    $view->with('categoryList', \App\Category::orderby('name', 'asc')->lists('name', 'id'));
		    
		});

		// Stock Movement Types
		view()->composer(array('stock_movements.index', 'stock_movements.create'), function($view) {
		    
		    $view->with('movement_typeList', \App\StockMovement::stockmovementList());
		    
		});

		// Document Types
		view()->composer(array('sequences.index', 'sequences.create', 'sequences.edit'), function($view) {
		    
		    $view->with('document_typeList', \App\Sequence::documentList());
		    
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
