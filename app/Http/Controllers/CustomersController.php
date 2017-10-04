<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Customer as Customer;
use App\Address as Address;
use View;

class CustomersController extends Controller {


   protected $customer, $address;

   public function __construct(Customer $customer, Address $address)
   {
        $this->customer = $customer;
        $this->address  = $address;
   }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $customers = $this->customer
                        ->with('address')
                        ->with('address.country')
                        ->with('address.state')
                        ->with('currency')
                        ->get();
        
        return view('customers.index', compact('customers'));
        
    }
	
    public function indexAjaxFiltered()
    {
        // return Input::get('filter_query');
		
// dump the next executed query and die (dd)
Event::listen('illuminate.query', function($sql)
{
    // dd($sql);
}); 

		$customers = $this->customer
						 ->where('name_commercial', 'like', '%'.Input::get('filter_query').'%')
//						 ->orWhere('reference', 'like', '%'.Input::get('filter_query').'%')
						 ->orderBy('name_commercial', 'asc')
						 ->lists('name_commercial', 'id');
//						 ->get();
		
//		$taxList = Tax::lists('name', 'id');		// http://four.laravel.com/docs/queries#selects
// echo '<pre>';
// print_r($customers);
// echo '</pre>';

        $response = array();

        foreach ($customers as $customer)
        {
            // list($response[]) = $customer;
			// list($key, $val) = each($customer);
			$response[] = $customer;
        }
// echo '<pre>';
// print_r($response);
// echo '</pre>';die();

        echo json_encode($response);
		
		// return View::make('admin.products.listing', compact('products', 'taxList'));
    }
	

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $action = $request->input('nextAction', '');

        $request->merge( ['outstanding_amount_allowed' => \App\Configuration::get('DEF_OUTSTANDING_AMOUNT')] );
        if ( !$request->input('address.name_commercial') ) {
            $request->merge( ['address.name_commercial' => $request->input('name_fiscal'),
                                'name_commercial' => $request->input('name_fiscal')] );
        }
        $this->validate($request, Customer::$rules);

        $request->merge( ['address.alias' => l('Main Address', [],'addresses')] );

        $this->validate($request, Address::related_rules());

        if ( !$request->has('currency_id') ) $request->merge( ['currency_id' => \App\Configuration::get('DEF_CURRENCY')] );

        if ( !$request->has('payment_day') ) $request->merge( ['payment_day' => null] );

        // ToDO: put default accept einvoice in a configuration key
        
        $customer = $this->customer->create($request->all());

        $data = $request->input('address');

        $address = $this->address->create($data);
        $customer->addresses()->save($address);

        $customer->invoicing_address_id = $address->id;
        $customer->shipping_address_id  = $address->id;
        $customer->save();

        if ($action == 'completeCustomerData')
            return redirect('customers/'.$customer->id.'/edit')
                ->with('success', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $customer->id], 'layouts') . $request->input('name_fiscal'));
        else
            return redirect('customers')
                ->with('success', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $customer->id], 'layouts') . $request->input('name_fiscal'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $sequenceList = \App\Sequence::listFor('Customer');

        $customer = $this->customer->with('addresses', 'address', 'address.country', 'address.state')->findOrFail($id); 

        $aBook       = $customer->addresses;
        $mainAddressIndex = -1;
        $aBookCount = $aBook->count();

        if ( !($aBookCount>0) )
        {
            // Empty Addresss Book!
            // $aBook       = array();
            $mainAddress = 0;

            // Sanitize
            if ( !( $customer->invoicing_address_id ) OR !( $customer->shipping_address_id) ) {
                $customer->invoicing_address_id = 0;
                $customer->shipping_address_id  = 0;
                $customer->save();
            }

            // Issue Warning!
            return View::make('customers.edit', compact('customer', 'aBook', 'mainAddressIndex'))
                ->with('warning', l('You need one Address at list, for Customer (:id) :name', ['id' => $customer->id, 'name' => $customer->name_fiscal]));
        };

        if ( $aBookCount == 1 ) 
        {
            // Only 1 address in Addresss Book!
            $warning = array();
            $addr = $aBook->first();
            if( $customer->shipping_address_id != $addr->id)
            {
                $customer->shipping_address_id  = $addr->id;
                // $customer->save();
                $warning[] = l('Shipping Address has been updated for Customer (:id) :name', ['id' => $customer->id, 'name' => $customer->name_fiscal]);
            }
            if( $customer->invoicing_address_id != $addr->id)
            {
                $customer->invoicing_address_id  = $addr->id;
                // $customer->save();
                $warning[] = l('Main Address has been updated for Customer (:id) :name', ['id' => $customer->id, 'name' => $customer->name_fiscal]);
            }
            if ( $customer->isDirty() ) $customer->save();   // Model has changed

            $mainAddressIndex = 0;

            return View::make('customers.edit', compact('customer', 'aBook', 'mainAddressIndex', 'sequenceList'))
                ->with('warning', $warning);

        } else {
            // So far, so good => full stack Address Book!
            $warning = array();
            // Check Shpping Address
            if ( !$aBook->contains($customer->shipping_address_id) )
            {
                if ($customer->shipping_address_id != 0) 
                {
                    $customer->shipping_address_id  = 0;
                    $warning[] = l('Default Shipping Address has been updated for Customer (:id) :name', ['id' => $customer->id, 'name' => $customer->name_fiscal]);
                }
            }
            // Check Invoicing Address
            if ( !$aBook->contains($customer->invoicing_address_id) )
            {
                if ($customer->invoicing_address_id != 0) 
                {
                    $customer->invoicing_address_id  = 0;
                    $warning[] = l('You should set the Main Address for Customer (:id) :name', ['id' => $customer->id, 'name' => $customer->name_fiscal]);
                }
            }
            if ( $customer->isDirty() ) $customer->save();   // Model has changed

            $mainAddr = $customer->invoicing_address_id;

            // Get index for drop-down selector
            foreach ($aBook as $key => $value) {
                if ($mainAddr == $value->id) {
                    $mainAddressIndex = $key;
                    break;
                }
            }
            if ($mainAddressIndex < 0) ; // Issue warning!
        }

        // echo '<pre>'; print_r($aBook); echo '</pre>'; die();
        // echo '<pre>'; print_r($customer); echo '</pre>'; die();

//        abi_r($sequenceList1, true);

        return view('customers.edit', compact('customer', 'aBook', 'mainAddressIndex', 'sequenceList'))
                ->with('warning', $warning);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $action = $request->input('nextAction', '');

        $section =  $request->input('tab_name')     ? 
                    '#'.$request->input('tab_name') :
                    '';

        if ($section == '#addressbook')
        {
            $input = [];
            $input['invoicing_address_id'] = $request->input('invoicing_address_id', 0); // Should be in address Book
            $input['shipping_address_id']  = $request->input('shipping_address_id', 0);  // Should be in address Book or 0

            $rules['invoicing_address_id'] = 'exists:addresses,id,addressable_id,'.intval($id);
            if ($input['shipping_address_id']>0)
//                $rules['shipping_address_id'] = 'exists:addresses,id,addressable_type,\\App\\Customer|exists:addresses,id,addressable_id,'.intval($id);
                $rules['shipping_address_id'] = 'exists:addresses,id,addressable_id,'.intval($id);
            else
                $input['shipping_address_id'] = 0;

             $this->validate($request, $rules);

                $customer = $this->customer->find($id);
                $customer->update($input);

                return redirect(route('customers.edit', $id) . $section)
                    ->with('info', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name_commercial'));

        }

        
        $customer = $this->customer->with('address')->findOrFail($id);
        $address = $customer->address;

        $this->validate($request, Customer::$rules);
        
//        $this->validate($request, Address::related_rules());

        $request->merge( ['name_commercial' => $request->input('address.name_commercial')] );

        // $customer->update( array_merge($request->all(), ['name_commercial' => $request->input('address.name_commercial')] ) );
        $customer->update( $request->all() );
        if ( !$request->input('address.name_commercial') ) $request->merge( ['address.name_commercial' => $request->input('name_fiscal')] );
        $data = $request->input('address');
        $address->update($data);


        if ($action != 'completeCustomerData')
            return redirect('customers/'.$customer->id.'/edit'.$section)
                ->with('info', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name_commercial'));
        else
            return redirect('customers')
                ->with('info', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name_commercial'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $c = $this->customer->find($id);

        // Addresses
        $c->addresses()->delete();

        // Customer
        $c->delete();

        return redirect('customers')
				->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
    }

    

    /**
     * Return a json list of records matching the provided query
     * @return json
     */
    public function ajaxCustomerSearch(Request $request)
    {
        $params = array();
        if (intval($request->input('name_commercial', ''))>0)
            $params['name_commercial'] = 1;
        
        return Customer::searchByNameAutocomplete($request->input('query'), $params);
    }

}