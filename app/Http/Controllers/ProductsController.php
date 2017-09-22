<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Product as Product;
use View, Form, DB;

class ProductsController extends Controller {


   protected $product;

   public function __construct(Product $product)
   {
        $this->product = $product;
   }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $products = $this->product->filter( $request->all() )
                                  ->with('combinations')                                  
                                  ->with('category')
                                  ->with('tax');
                                  

        if ( $request->input('reference') && trim($request->input('reference')) !== '' )
            $products = $products->orWhereHas('combinations', function($q) use ($request)
                                                {
                                                    // http://stackoverflow.com/questions/20801859/laravel-eloquent-filter-by-column-of-relationship
                                                    $q->where('reference', 'LIKE', '%' . trim($request->input('reference')) . '%');
                                                }
                                              );  // ToDo: if name is supplied, shows records that match reference but do not match name (due to orWhere condition)


        $products = $products->paginate( \App\Configuration::get('DEF_ITEMS_PERPAGE') );

        $products->setPath('products');     // Customize the URI used by the paginator

        return View::make('products.index', compact('products'));
        
    }
    public function indexFiltered()
    {
        // return Input::get('filter_query');

		$products = $this->product
						 ->where('name', 'like', '%'.Input::get('filter_query').'%')
						 ->orWhere('reference', 'like', '%'.Input::get('filter_query').'%')
						 ->orderBy('name', 'asc')
						 ->paginate(2);
//						 ->get();
		
		$taxList = Tax::pluck('name', 'id');		// http://four.laravel.com/docs/queries#selects    
													// $catlist = Category::where('type', $content_type)->pluck('name');

        return View::make('products.listing', compact('products', 'taxList'));
		
		
		
		/*
        // 
		$input = Input::all();
		
		// 
		$filter_query = implode(' - ', $input);
		
		// $filter_query = Input::get('filter_query');
		
		// $filter_query = 
		
		// return Response::json(Input::get('filter_query'));
		
		// return 'abadaba';
		
		return $filter_query.' xTETAx';
		$products = $this->product->all();
        // $products = $this->product->orderBy('percent', 'desc')->get();
		
		$taxList = Tax::pluck('name', 'id');		// http://four.laravel.com/docs/queries#selects    
													// $catlist = Category::where('type', $content_type)->pluck('name');

        // $taxName = Tax::query()->where_id(1)->get(array('username'));
		
		return View::make('products.index', compact('products', 'taxList'));
		*/
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // if ( !( $request->input('cost_average') > 0 ) ) $request->merge( ['cost_average' => $request->input('cost_price')] );

        $action = $request->input('nextAction', '');

        if ( !$request->has('measure_unit') ) 
            $request->merge( ['measure_unit' => \App\Configuration::get('DEF_MEASURE_UNIT')] );

        $rules = Product::$rules['create'];
        if ($request->input('quantity_onhand')>0)
            $rules['warehouse_id'] .= '|exists:warehouses,id';

        $this->validate($request, $rules);

        // If sequences are used:
        //
        // $product_sequences = \App\Sequence::listFor(\App\Product::class);

        // Create Product
//        $product = $this->product->create($request->except('quantity_onhand'));
        $product = $this->product->create($request->all());

/*
        // Create stock movement (Initial Stock)
        $data = [   'date' =>  \Carbon\Carbon::now(), 
                    'document_reference' => '', 
                    'price' => $request->input('price'), 
                    'quantity' => $request->input('quantity_onhand'),  
                    'notes' => '',
                    'product_id' => $product->id, 
                    'warehouse_id' => $request->input('warehouse_id'), 
                    'movement_type_id' => 10,
                    'model_name' => '', 'document_id' => 0, 'document_line_id' => 0, 'combination_id' => 0, 'user_id' => \Auth::id()
        ];

        // Initial Stock
        $stockmovement = \App\StockMovement::create( $data );

        // Stock movement fulfillment (perform stock movements)
        $stockmovement->fulfill();

*/
        // Prices according to Ptice Lists
        $plists = \App\PriceList::get();

        foreach ($plists as $list) {

            $price = $list->calculatePrice( $product );
            // $product->pricelists()->attach($list->id, array('price' => $price));
            $line = \App\PriceListLine::create( [ 'product_id' => $product->id, 'price' => $price ] );

            $list->pricelistlines()->save($line);
        }

        if ($action == 'completeProductData')
        return redirect('products/'.$product->id.'/edit')
                ->with('success', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $product->id], 'layouts') . $request->input('name'));
        else
        return redirect('products')
                ->with('success', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $product->id], 'layouts') . $request->input('name'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $product = $this->product
                        ->with('tax')
                        ->with('warehouses')
                        ->with('combinations')
                        ->with('combinations.options')
                        ->with('combinations.options.optiongroup')
                        ->findOrFail($id);

        // Gather Attributte Groups
        $groups = array();

        if ( $product->combinations->count() )
        {
            foreach ($product->combinations as $combination) {
                foreach ($combination->options as $option) {
                    $groups[$option->optiongroup->id]['name'] = $option->optiongroup->name;
                    $groups[$option->optiongroup->id]['values'][$option->optiongroup->id.'_'.$option->id] = $option->name;
                }
            }
        } else {
            $groups = \App\OptionGroup::has('options')->orderby('position', 'asc')->pluck('name', 'id');
        }

        $pricelists = \App\PriceList::with('currency')->orderBy('id', 'ASC')->get();

        return view('products.edit', compact('product', 'groups', 'pricelists'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
 /*   {
        $product = Product::findOrFail($id);

        if ( isset(Product::$rules['main_data']['reference']) ) Product::$rules['main_data']['reference'] .= $product->id;

        // ToDo: reference should be '' for products with combinations (not required, not unique)
        $this->validate($request, Product::$rules[ $request->input('tab_name') ]);

        $product->update($request->all());

        return redirect('products')
                ->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name'));
    } */
    {
        $product = Product::findOrFail($id); // echo $request->input('tab_name'); die();

        $vrules = Product::$rules[ $request->input('tab_name') ];

        if ( isset($vrules['reference']) ) $vrules['reference'] .= $product->id;

        if ($product->product_type == 'combinable') 
        {
            // Remove reference field
            $request->merge(array('reference' => ''));
            unset( $vrules['reference'] );
        }
        // dd($vrules);

        $this->validate($request, $vrules);

        $product->update($request->all());

        return redirect('products/'.$id.'/edit'.'#'.$request->input('tab_name'))
                ->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $product->name);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // Any Documents? If any, cannot delete, only disable

        // Delete combinations

        // Delete Images

        $this->product->findOrFail($id)->delete();

        return redirect('products')
                ->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
    }

    /**
     * Make Combinations for specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function combine($id, Request $request)
    {
        $groups = $request->input('groups');

        // Validate: $groups ahold not be empty, and values should match options table
        // http://laravel.io/forum/11-12-2014-how-to-validate-array-input
        // https://www.google.es/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8&client=ubuntu#q=laravel%20validate%20array%20input

        $product = $this->product->findOrFail($id);

        // Start Combinator machime!

        $data = array();

        foreach ( $groups as $group ) 
        {
            $data[] = \App\Option::where('option_group_id', '=', $group)->orderby('position', 'asc')->pluck('id');
        }

        $combos = combos($data);

        foreach ( $combos as $combo ) 
        {
            $combination = \App\Combination::create(array());
            $product->combinations()->save($combination);

            $combination->options()->attach($combo);;
        }

        // Combinations are created with stock = 0. 
        // Create combinations only alollowed if product->quantity_onhand = 0 

        $product->reference = '';
        $product->product_type = 'combinable';
        $product->save();



        return redirect('products/'.$combination->product_id.'/edit#combinations')
                ->with('success', l('This record has been successfully combined &#58&#58 (:id) ', ['id' => $id], 'layouts') . $product->name);
    }

    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxProductSearch(Request $request)
    {
        if ($request->has('query'))
        {
            $onhand_only = ( $request->has('onhand_only') ? 1 : 0 );

            return Product::searchByNameAutocomplete($request->input('query'), $onhand_only);
        }


        $products = Product::select('id', 'name as label', 'reference')->orderBy('products.name')->where('name', 'like', '%' . $request->input('term') . '%')->get();
        
        return json_encode( $products );
    }

    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxProductOptionsSearch(Request $request)
    {
        $product = $this->product
                        ->with('tax')
                        ->with('combinations')
                        ->with('combinations.options')
                        ->with('combinations.options.optiongroup')
                        ->findOrFail($request->input('product_id'));

        // Gather Attributte Groups
        $groups = array();

        if ( $product->combinations->count() )
        {
            foreach ($product->combinations as $combination) {
                foreach ($combination->options as $option) {
                    $groups[$option->optiongroup->id]['name'] = $option->optiongroup->name;
                    // $groups[$option->optiongroup->id]['values'][$option->optiongroup->id.'_'.$option->id] = $option->name;
                    $groups[$option->optiongroup->id]['values'][$option->id] = $option->name;
                }
            }
        }

        $str = '';

        foreach ($groups as $i => $group) {
        
            $str .= Form::label('group['.$i.']', $group['name']) . 
                    '<div class="option_list">' . 
                    Form::select('group['.$i.']', array('0' => l('-- Please, select --', [], 'layouts')) + $group['values'], null, array('class' => 'form-control option_select')) . 
                    '</div>';
        
        }
        return '<div id="options">' . $str . '</div>';

        // sleep(5);
        // return '<select class="form-control" id="warehouse_id" name="warehouse_id"><option value="0">-- Seleccione --</option><option value="1">Main Address</option><option value="8">CALIMA 25</option></select><select class="form-control" id="warehouse_id" name="warehouse_id"><option value="0">-- Seleccione --</option><option value="1">Main Address</option><option value="8">CALIMA 25</option></select><select class="form-control" id="warehouse_id" name="warehouse_id"><option value="0">-- Seleccione --</option><option value="1">Main Address</option><option value="8">CALIMA 25</option></select>';
        // return 'Hello World! '.$request->input('product_id');

        /*

SELECT combination_id, COUNT(combination_id) tot FROM `combinations` as c
left join combination_option as co
on co.combination_id = c.id
WHERE c.product_id = 15
AND (co.option_id = 4) or (co.option_id = 10) or 1
GROUP BY combination_id ORDER BY tot DESC
LIMIT 1

SELECT page, COUNT(page ) totpages
FROM visitas
GROUP BY page ORDER BY totpages DESC
LIMIT 1

        */
    }

    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxProductCombinationSearch(Request $request)
    {
        if ($request->has('group')) {
            $combination_id = \App\Combination::getCombinationByOptions( $request->input('product_id'), $request->input('group') );

            // ToDo: what happens if $combination_id=0 -> Failed to load resource: the server responded with a status of 500 (Internal Server Error)  http://localhost/aBillander5/public/products/ajax/combination_lookup
            $combination = \App\Combination::select('id', 'product_id', 'reference')
                            ->where('id', '=', $combination_id)
                            ->Where('product_id', '=', $request->input('product_id'))
                            ->take(1)->get();
            return json_encode( $combination[0] );

        } else {
            $combination_id = 0;

        }
    }

    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxProductPriceSearch(Request $request)
    {
        $product_id = $request->input('product_id');
        $customer_id = $request->input('customer_id');
        $product_string = $request->input('product_string');

    //    $product = \App\Product::find();

        $product = $this->product
                        ->with('tax')
                        ->with('combinations')
                        ->with('combinations.options')
                        ->with('combinations.options.optiongroup')
                        ->findOrFail(intval($product_id));
                        
        $tax = $product->tax;

        $customer = \App\Customer::findOrFail(intval($customer_id));

        // Calculate priece per $customer_id now!
        $product->price_customer = $product->price( $customer );
        $tax_percent = $tax->percent;
        if ($customer->sales_equalization) $tax_percent += $tax->extra_percent;
        $product->price_customer_with_tax = $product->price_customer*(1.0+$tax_percent/100.0);

        // Add customer_price
        $p = json_decode( $product_string, true);
        $p = array_merge($p, ['price_customer' => $product->price_customer]);
        $product_string = json_encode($p);

        // return Product::searchByNameAutocomplete(Input::get('query'));
        return View::make('products.ajax.show_price', compact('product', 'tax', 'customer', 'product_string'));
    }

}

/*    http://stackoverflow.com/questions/16661292/add-new-methods-to-a-resource-controller-in-laravel-4

Verb | Path | Action | Route Name 
 ???-|????????|?????|??????? 
GET  | /resource           | index | resource.index 
GET  | /resource/create    | create | resource.create 
POST | /resource           | store | resource.store 
GET  | /resource/{id}      | show | resource.show 
GET  | /resource/{id}/edit | edit | resource.edit 
PUT/PATCH | /resource/{id} | update | resource.update 
DELETE | /resource/{id}    | destroy | resource.destroy

*/