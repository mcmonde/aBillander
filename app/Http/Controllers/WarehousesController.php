<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Warehouse as Warehouse;
use App\Address as Address;
use App\Country as Country;
use View;

class WarehousesController extends Controller {


   protected $warehouse;
   protected $address;

   public function __construct(Warehouse $warehouse, Address $address)
   {
        $this->warehouse = $warehouse;
        $this->address = $address;
   }

	/**
	 * Display a listing of the resource.
	 * GET /warehouses
	 *
	 * @return Response
	 */
	public function index()
	{
        $warehouses = $this->warehouse->with('address')->get();

        return view('warehouses.index', compact('warehouses'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /warehouses/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('warehouses.create'); 
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /warehouses
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, $this->warehouse::$rules);
		$this->validate($request, $this->address::related_rules());

		$warehouse = $this->warehouse->create(['notes' => $request->input('address.notes'), 'alias' => $request->input('address.alias'), 'active' => $request->input('active')]);

		$data = $request->input('address');
		$address = $this->address->create( $data );
		$warehouse->addresses()->save($address);

		return redirect('warehouses')
				->with('info', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $warehouse->id], 'layouts') . $request->input('name'));
	}

	/**
	 * Display the specified resource.
	 * GET /warehouses/{id}
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
	 * GET /warehouses/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$warehouse = $this->warehouse->with('address')->findOrFail($id);

        $country = Country::find( $warehouse->address->country_id );

        // abi_r($country);

        $stateList = $country ? $country->states()->pluck('name', 'id')->toArray() : [] ;

        // abi_r($stateList, true);
		
		return view('warehouses.edit', compact('warehouse', 'stateList'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /warehouses/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$this->validate($request, $this->warehouse::$rules);
		$this->validate($request, $this->address::related_rules());

		$warehouse = $this->warehouse->findOrFail($id);
		$address = $warehouse->address;
		
		$warehouse->update( ['notes' => $request->input('address.notes'), 'alias' => $request->input('address.alias'), 'active' => $request->input('active')] );

		// abi_r($request->all(), true);

		$data = $request->input('address');
		$address->update( $data );

		return redirect('warehouses')
				->with('info', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name_commercial'));
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /warehouses/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $this->warehouse->findOrFail($id)->delete();

        return redirect('warehouses')
				->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexProducts($id)
	{
        $wh = Warehouse::find($id);

        if (is_null($wh)) 
        {
        	return Redirect::route('warehouses.index');
       	}

        return View::make('warehouses.indexProducts')->with(array('warehouse' => $wh, 'products' => $wh->products));
	}
	
	public function indexStockmoves($id)
	{
        $wh = Warehouse::find($id);

        if (is_null($wh)) 
        {
        	return Redirect::route('warehouses.index');
       	}

        return View::make('warehouses.indexStockmoves')->with(array('warehouse' => $wh, 'stockmoves' => $wh->stockmoves));
	}

}