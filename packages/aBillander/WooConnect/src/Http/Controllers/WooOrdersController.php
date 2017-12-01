<?php 

namespace aBillander\WooConnect\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


use Illuminate\Http\Request;

use WooCommerce;
// use Automattic\WooCommerce\HttpClient\HttpClient\HttpClientException;

class WooOrdersController extends Controller {


   protected $currency;

   public function hello()
   {
        return \aBillander\WooConnect\WooConnector::getStatusList();

        try {



			$results = WooCommerce::get('orders');

			$products = WooCommerce::get('products');

			$customers = WooCommerce::get('customers');



			$result = count($results);

			$customer = count($customers);

			$product = count($products);



			//you can set any date which you want

			$query = ['date_min' => '2017-10-01', 'date_max' => '2017-10-30'];

			$sales = WooCommerce::get('reports/sales', $query);

			$sale = $sales[0]["total_sales"];

		}



			catch(HttpClientException $e) {

			$e->getMessage(); // Error message.

			$e->getRequest(); // Last request data.

			$e->getResponse(); // Last response data.

		}

 //       abi_r($endpoints, true);

		return view('woo_connect::woo_connect.hello', compact('results', 'result', 'customers', 'customer', 'products', 'product', 'sale'));

		// Order status. Options: pending, processing, on-hold, completed, cancelled, refunded and failed. Default is pending.

		// WooCommerce, en la instalación por defecto, incluye siete estados distintos en los que un pedido puede encontrarse:

    	// Completado,     Pendiente de pago,    En espera,    Procesando,    Cancelado,    Reembolsado,    Fallido

    	// https://www.enriquejros.com/estados-de-pedido-woocommerce/


   }

   public function __construct()
   {
        //
   }

	/**
	 * Display a listing of the resource.
	 * GET /currencies
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		// https://www.youtube.com/watch?v=IcLaNHxGTrs
		// $user = User::query();
		$queries = [];
		$columns = ['after', 'before', 'status'];

		// ToDo: convert dates to ISO8601 compliant date

		foreach ($columns as $column) {
			if (request()->has($column)) {
				// $users = $users->where($coulumn, request($column));
				$queries[$column] = request($column);
			}
		}

		// $users = $users->paginate(5)->appends($queries);


		$query = array_merge($request->query(), $queries);

		// https://shendinotes.wordpress.com/2017/01/13/manual-pagination-with-laravel-5/
		// https://laracasts.com/discuss/channels/laravel/laravel-pagination-not-working-with-array-instead-of-collection

		$page = Paginator::resolveCurrentPage();  // $request->input('page', 1); // Get the current page or default to 1
		$perPage = intval(\App\Configuration::get('WOO_ORDERS_PER_PAGE'));
		if ($perPage<1) $perPage=10;
		$offset = ($page * $perPage) - $perPage;

		// https://stackoverflow.com/questions/39101445/how-to-sort-products-in-woocommerce-wordpress-json-api
		// https://www.storeurl.com/wc-api/v3/products?orderby=title&order=asc
		// https://www.storeurl.com/wc-api/v3/products?filter[order]=asc&filter[orderby]=meta_value_num&filter[orderby_meta_key]=_regular_price  =>  the "Filter" parameter, which allows you to use any WP_Query style arguments you may want to add to your request
		$params = [
		    'per_page' => $perPage,
		    'page' => $page,
//		    'status' => 'completed',
//	    	'after'  => '2017-08-01 00:00:00',		//  ISO8601 compliant date
//	    	'before' => '2017-12-31T23:59:59',
//	    	'orderby' => 'id',
//	    	'order'   => 'asc',
		];

		foreach ($columns as $column) {
			if (request()->has($column) && request($column)) {
				// $users = $users->where($coulumn, request($column));
				$params[$column] = request($column);
			}
		}

		// abi_r($params, true);

		$results = WooCommerce::get('orders', $params);
		$total = WooCommerce::totalResults();


		$orders = new LengthAwarePaginator($results, $total, $perPage, $page, ['path' => $request->url(), 'query' => $query]);

		// $orders = collect($results);

        return view('woo_connect::woo_orders.index', compact('orders', 'query'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /currencies/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('currencies.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /currencies
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, Currency::$rules);

		$currency = $this->currency->create($request->all());

		\App\CurrencyConversionRate::create([
				'date' => \Carbon\Carbon::now(), 
				'currency_id' => $currency->id, 
				'conversion_rate' => $currency->conversion_rate, 
				'user_id' => \Auth::id(),
			]);

		return redirect('currencies')
				->with('info', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $currency->id], 'layouts') . $request->input('name'));
	}

	/**
	 * Display the specified resource.
	 * GET /currencies/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// print_r(json_decode($json));       -> stdClass Object
		// print_r(json_decode($json, true)); -> Array

		// $order = new \StdClass();

		$order = WooCommerce::get('orders/'.$id);	// Array

		return view('woo_connect::woo_orders.show', compact('order'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /currencies/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$currency = $this->currency->findOrFail($id);
		
		return view('currencies.edit', compact('currency'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /currencies/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$query  = $request->query();
		$status = $request->input('order_status');

		$data = [
		    'status' => $status,
		];

		WooCommerce::put('orders/'.$id, $data);

		return redirect()->route('worders', $query)
				->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . ' ['.$status.']');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /currencies/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $this->currency->findOrFail($id)->delete();

        // Delete currency conversion rate history

        return redirect('currencies')
				->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
	}


/* ********************************************************************************************* */   

	/**
	 * Show the form for creating a new resource.
	 * GET /currencies/create
	 *
	 * @return Response
	 */
	public function getStatuses()
	{
		// abi_r('x', true);
        try {
        	$results = WooCommerce::get('orders/statuses');
		}



		catch(HttpClientException $e) {

			abi_r($e->getMessage(), true);

			$e->getMessage(); // Error message.

			$e->getRequest(); // Last request data.

			$e->getResponse(); // Last response data.

			abi_r($e->getMessage(), true);

		}

		return $results;
	} 


    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxCurrencyRateSearch(Request $request)
    {
        // Request data
        $currency_id     = $request->input('currency_id');
        
        $currency = Currency::find(intval($currency_id));

        if ( !$currency ) {
            // Die silently
            return '';
        }

        return $currency->conversion_rate;
    }

}