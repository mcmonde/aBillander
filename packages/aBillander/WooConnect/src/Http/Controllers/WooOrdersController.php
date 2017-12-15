<?php 

namespace aBillander\WooConnect\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;

use WooCommerce;
use Automattic\WooCommerce\HttpClient\HttpClientException as WooHttpClientException;

use \aBillander\WooConnect\WooOrder;

class WooOrdersController extends Controller {


   protected $order;

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

   public function __construct( WooOrder $order )
   {
        $this->order = $order;
   }

	/**
	 * Display a listing of the resource.
	 * GET /worders
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		// https://www.youtube.com/watch?v=IcLaNHxGTrs
		// $user = User::query();
		$queries = [];
		$columns = ['after', 'before', 'status'];
		$column_dates = ['after', 'before'];

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

		foreach ($column_dates as $column) {
			if (isset($params[$column])) {
				$params[$column] .= ' 00:00:00';	// Convert date to ISO8601 compliant date
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
	 * GET /worders/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /worders
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /worders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// print_r(json_decode($json));       -> stdClass Object
		// print_r(json_decode($json, true)); -> Array

		// $order = new \StdClass();

		// https://www.tychesoftwares.com/how-to-add-prefix-or-suffix-to-woocommerce-order-number/
		// https://www.tychesoftwares.com/how-to-reset-woocommerce-order-numbers-every-24-hours/
		// https://www.tychesoftwares.com/add-new-column-woocommerce-orders-page/

		try {
			$wc_currency = \App\Currency::findOrFail( intval(\App\Configuration::get('WOOC_DEF_CURRENCY')) );
		} catch (ModelNotFoundException $ex) {
			// If Currency does not found. Not any good here...
			$wc_currency = \App\Context::getContext()->currency;	// Or fallback to Configuration::get('DEF_CURRENCY')
		}

		$params = [
		    'dp'   => $wc_currency->decimalPlaces,
		];

		$order = WooCommerce::get('orders/'.$id, $params);	// Array

		// I am thirsty. Let's get hydrated!
		$vatNumber = WooOrder::getVatNumber( $order );
		$order['billing']['vat_number'] = $vatNumber;

		$date_downloaded = '';
		foreach($order['meta_data'] as $meta) {
			if ($meta['key']=='date_abi_exported') {
				$date_downloaded = $meta['key'];
				break;
			}
		}
		$order["date_downloaded"] = $date_downloaded;

		$country = \App\Country::findByIsoCode( $order['billing']['country'] );
		$order['billing']['country_name'] = $country ? $country->name : $order['billing']['country'];

		$state = \App\State::findByIsoCode( (strpos($order['billing']['state'], '-') ? '' : $order['billing']['country'].'-').$order['billing']['state'] );
		$order['billing']['state_name'] = $state ? $state->name : $order['billing']['state'];

		return view('woo_connect::woo_orders.show', compact('order'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /worders/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /worders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$query    = $request->query();
		$status   = $request->input('order_status');
		$set_paid = $request->input('order_set_paid', 0);	// set_paid 	boolean 	Define if the order is paid. It will set the status to processing and reduce stock items. Default is false. 

//		abi_r((bool) $set_paid, true);

		$data = [
		    'status'   => $status,
		    'set_paid' => (boolean) $set_paid,
		    'meta_data' => [[									// Meta para saber cuándo fue descargada
		    	'key'   => 'date_abi_exported',
                'value' => (string) \Carbon\Carbon::now(),
             ]],
		];

		WooCommerce::put('orders/'.$id, $data);

		return redirect()->route('worders.index', $query)
				->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . ' ['.$status.']');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /worders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        //
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /worders/{id}/import
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function import($id)
	{
		$importer = \aBillander\WooConnect\WooOrderImporter::makeInvoice( $id ); die();

		// Get Order fromm WooCommerce Shop
        try {

			$order = WooCommerce::get('orders/'.intval($id));	// Array
		}

		catch( WooHttpClientException $e ) {

			/*
			$e->getMessage(); // Error message.

			$e->getRequest(); // Last request data.

			$e->getResponse(); // Last response data.
			*/

			return redirect()->route('worders.index')
					->with('error', $e->getMessage()." (id=$id)");

		}

		// Save
		$data = [
            'id' => $order['id'],

            'number'    => $order['number'],
            'order_key' => $order['order_key'],
            'currency'  => $order['currency'],

            'date_created'      => WooOrder::getDate( $order['date_created'] ),
            'date_abi_exported' => WooOrder::getExportedAt($order['meta_data']),

            'total'     => $order['total'],
            'total_tax' => $order['total_tax'],
            
            'customer_id'   => $order['customer_id'],
            'customer_note' => $order['customer_note'],

            'payment_method'        => $order['payment_method'],
            'payment_method_title'  => $order['payment_method_title'],
            'shipping_method_id'    => WooOrder::getShippingMethodId($order['shipping_lines']),
            'shipping_method_title' => WooOrder::getShippingMethodTitle($order['shipping_lines']),
		];

		
        try {

        	$wc_order = $this->order->updateOrCreate($data);
		}

		catch( \Exception $e ) {
			abi_r($e->getMessage());
		}


		// Customer stuff

		// abi_r($data, true);
		// abi_r($order, true);


		return redirect()->route('worders.show', $id)
				->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts'));
	}


/* ********************************************************************************************* */   

	/**
	 * Show the form for creating a new resource.
	 * GET /worders/create
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

}


/* ********************************************************************************************* */   

// Custom functions
/*
function getVatNumber( $order )
{
	$vn = '';
	foreach($order['meta_data']  as $data ) {
		if( $data['key'] == 'CIF/NIF' ) {
			$vn = $data['value'];
			break;
		}
	}

	return $vn;
}
*/

function getSpanish( $string )
{
	$sa = explode('[:es]', $string);
	$s = $sa[1];

	$i = strpos($s, '[');
	$s = substr($s, 0, $i);

	return $s;
}