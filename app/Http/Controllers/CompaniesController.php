<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Company as Company;
use App\Address as Address;
use App\Configuration; 
use View;

class CompaniesController extends Controller {


   protected $company;

   public function __construct(Company $company)
   {
        $this->company = $company;
   }
	/**
	 * Display a listing of the resource.
	 * GET /companies
	 *
	 * @return Response
	 */
	public function index()
	{
		if ( intval(Configuration::get('DEF_COMPANY')) > 0 ) 
			return $this->edit(intval(Configuration::get('DEF_COMPANY')));
		else
			return $this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /companies/create
	 *
	 * @return Response
	 */
	public function create()
	{
        if ( Configuration::get('DEF_COMPANY') > 0 ) {
        	// Company already created
        	return $this->edit(intval(Configuration::get('DEF_COMPANY')));
        } else {
        
            $company = $this->company;
    
            return View::make('companies.create', compact('company'));
        }
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /companies
	 *
	 * @return Response
	 */
	public function store()
	{
        $input = Input::all();

        $v = Validator::make($input, Company::$rules);

        if ($v->passes())
        {
            $this->company = $this->company->create($input);

            Configuration::updateValue('DEF_COMPANY', $this->company->id);

            return Redirect::route('companies.index')
				->with('info', 'El registro se ha creado correctamente: '. $input['name_fiscal']);
        }

        return Redirect::route('companies.create')
            ->withInput()
            ->withErrors($v)
            ->with('message_error', 'There were validation errors');
	}

	/**
	 * Display the specified resource.
	 * GET /companies/{id}
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
	 * GET /companies/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $company = $this->company = Company::with('Address')->with('Currency')->findOrFail( intval(Configuration::get('DEF_COMPANY')) );
    
        return View::make('companies.edit', compact('company'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /companies/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		if (!$request->input('name_commercial')) $request->merge( ['name_commercial' => $request->input('name_fiscal')] );

		$company = Company::findOrFail($id);
		$address = Address::findOrFail($company->address_id);

		$this->validate($request, Company::$rules);
			$request->merge( ['alias' => $address->alias] );		// Alias is mandatory!
		$this->validate($request, ['address' => Address::$rules]);

		$company->update($request->all());
			$request->merge($request->input('address'));   // also replace
		$address->update($request->except(['address']));

		return redirect('companies')
				->with('info', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name_commercial'));
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /companies/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}