<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Customer as Customer;
use App\CustomerInvoice as CustomerInvoice;
use App\CustomerInvoiceLine as CustomerInvoiceLine;
use View;

use App\Configuration as Configuration;

class CustomerInvoicesController extends Controller {


   protected $customer, $customerInvoice, $customerInvoiceLine;

   public function __construct(Customer $customer, CustomerInvoice $customerInvoice, CustomerInvoiceLine $customerInvoiceLine)
   {
        $this->customer = $customer;
        $this->customerInvoice = $customerInvoice;
        $this->customerInvoiceLine = $customerInvoiceLine;
   }

	/**
	 * Display a listing of customer_invoices
	 *
	 * @return Response
	 */
	public function index()
	{
        $customer_invoices = $this->customerInvoice
							->with('customer')
							->with('currency')
							->with('paymentmethod')
							->orderBy('id', 'desc')->get();

		return view('customer_invoices.index', compact('customer_invoices'));
	}

	/**
	 * Show the form for creating a new customerinvoice
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		if ( $request->has('customer_id') ) { 

        	$sequenceList = \App\Sequence::listFor('CustomerInvoice');

	        if ( !$sequenceList )
	            return redirect('customerinvoices')
	                ->with('error', l('There is not any Sequence for this type of Document &#58&#58 You must create one first', [], 'layouts'));

        	$payments = \App\PaymentMethod::count();

	        if ( !$payments )
	            return redirect('customerinvoices')
	                ->with('error', l('There is not any Payment Method &#58&#58 You must create one first', [], 'layouts'));

			$customer = \App\Customer::with('addresses')->findOrFail( $request->input('customer_id') );
		//	Session::flash('error', 'El Cliente <b>'.$customer->name_fiscal.' ('.$customer->id.')</b>no existe.');
		//	return View::make('customer_invoices.index')->with('success', 'El Cliente <b>'.$customer->name_fiscal.' ('.$customer->id.')</b>no existe.');
			
			// Prepare & retrieve customer data to fill in the form
			// Should check Invoicing Address (at least)
			$aBook       = $customer->addresses;

			$team = $customer->invoicing_address_id;
			$invoicing_address = $aBook->filter(function($item) use ($team) {	// Filter returns a collection!
			    return $item->id == $team;
			})->first();

			$addressbookList = array();
			foreach ($aBook as $address) {
				$addressbookList[$address->id] = $address->alias;
			}

			$currency_id = $customer->currency_id > 0 ? $customer->currency_id : \App\Context::getContext()->currency->id;
	        try {

	            $currency = \App\Currency::findOrFail($currency_id);

	        } catch(ModelNotFoundException $e) {

	            $currency = \App\Context::getContext()->currency;
	        }

			// Prepare Customer Invoice default data
			$invoice = $this->customerInvoice;

			$invoice->sequence_id          = $customer->sequence_id > 0 ? $customer->sequence_id : Configuration::get('DEF_CUSTOMER_INVOICE_SEQUENCE');
			$invoice->customer_id          = $customer->id;

			$invoice->reference            = '';
			$invoice->document_discount    = 0.0;

			$invoice->document_date        = \Carbon\Carbon::now();
			$invoice->document_date_form   = abi_date_short( \Carbon\Carbon::now() );
			
			$invoice->delivery_date        = $invoice->document_date;
			$invoice->delivery_date_form   = $invoice->document_date_form;

			$invoice->number_of_packages   = 1;
			$invoice->shipping_conditions  = '';
			$invoice->tracking_number      = '';

			$invoice->currency_conversion_rate = $currency->conversion_rate;
			$invoice->down_payment         = 0.0;
			$invoice->open_balance         = 0.0;

			$invoice->total_tax_incl     = 0.0;
			$invoice->total_tax_excl     = 0.0;
			$invoice->commission_amount  = 0.0;

			$invoice->notes         = '';
			$invoice->status        = 'draft';
			$invoice->einvoice      = $customer->accept_einvoice;

			$invoice->printed       = 0;
			$invoice->posted        = 0;
			$invoice->paid          = 0;

			$invoice->invoicing_address_id = $customer->invoicing_address_id;
			$invoice->shipping_address_id  = $customer->shipping_address_id > 0 ? $customer->shipping_address_id : $customer->invoicing_address_id;
			$invoice->warehouse_id         = Configuration::get('DEF_WAREHOUSE');
			$invoice->carrier_id           = $customer->carrier_id  > 0 ? $customer->carrier_id  : Configuration::get('DEF_CARRIER');
			$invoice->sales_rep_id         = $customer->sales_rep_id > 0 ? $customer->sales_rep_id : 0;
			$invoice->currency_id          = $currency->id;
			$invoice->payment_method_id    = $customer->payment_method_id > 0 ? $customer->payment_method_id : Configuration::get('DEF_CUSTOMER_PAYMENT_METHOD');
			$invoice->template_id          = $customer->template_id > 0 ? $customer->template_id : Configuration::get('DEF_CUSTOMER_INVOICE_TEMPLATE');
			$invoice->parent_document_id   = null;

			$invoice->customerInvoiceLines = array();

			return View::make('customer_invoices.create', compact('customer', 'invoicing_address', 'aBook', 'addressbookList', 'invoice', 'sequenceList'));

		} else {
			// No Customer available, ask for one
			return View::make('customer_invoices.create');
		}
	}

	/**
	 * Store a newly created customerinvoice in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		if ( $request->has('submitCustomer_id')) 
		{
			$customer_id = intval($request->input('customer_id'));
			$customer = \App\Customer::findOrFail( $customer_id );
			
			// Valid Customer found: Redirect and collect invoice data
			return redirect('customerinvoices/create?customer_id='.$customer_id);
		}

		/* *********************************************************************** */

		// STEP 1 : validate data

		// Check Shipping Address
		if ( $request->input('shipping_address_id') < 1 ) 
			$request->replace( array('shipping_address_id' => $request->input('invoicing_address_id')) );

		$this->validate($request, CustomerInvoice::$rules);

		// STEP 2 : build objects
		
		if ( !$request->input('draft') ) {
			$seq = \App\Sequence::find( $request->input('sequence_id') );
			$doc_id = $seq->getNextDocumentId();
			$extradata = [	'document_prefix'      => $seq->prefix,
							'document_id'          => $doc_id,
							'document_reference'   => $seq->getDocumentReference($doc_id),
						 ];
			$request->merge( $extradata );
		}

		$dates = [
						'document_date' => \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $request->input('document_date_form') ),
						'delivery_date' => \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $request->input('delivery_date_form') ),
				 ];
		$request->merge( $dates );

		$statusdata = [ //  'einvoice' => $request->input('einvoice'),
						//	'einvoice_sent' => ( $request->input('einvoice') ? 0 : 1 ),	// Document sent. See also: field "edocument_sent_at"
							'printed' => 0,
							'customer_viewed' => 0,
							'posted' => 0,
					  ];
		$request->merge( $statusdata );

		$totals = [
						'total_discounts_tax_incl' => $request->input('order_gross_tax_incl') - $request->input('order_total_tax_incl'),
						'total_discounts_tax_excl' => $request->input('order_gross_tax_excl') - $request->input('order_total_tax_excl'),
						'total_products_tax_incl' => $request->input('order_gross_tax_incl'),
						'total_products_tax_excl' => $request->input('order_gross_tax_excl'),
						'total_tax_incl' => $request->input('order_total_tax_incl'),
						'total_tax_excl' => $request->input('order_total_tax_excl'),
				  ];
		$request->merge( $totals );

		// Open Balance
		$request->merge( array('open_balance' => floatval($request->input('total_tax_incl')) - floatval($request->input('down_payment')) ) );

		// ToDo: Calculate 'commission_amount' (maybe after line saving!)

		$customerInvoice = $this->customerInvoice->create($request->all());


		// 
		// Lines stuff
		// 
		$line = $this->customerInvoiceLine;

		// Loop...
		$n = intval($request->input('nbrlines'));

        for($i = 0; $i < $n; $i++)
        {
			if ( !$request->has('lineid_'.$i) ) continue;	// Line was deleted on View

			// $line = new CustomerInvoiceLine();
			$line = $this->customerInvoiceLine;

			$line->line_sort_order = $request->input('line_sort_order_'.$i);
			$line->line_type       = $request->input('line_type_'.$i);

			$line->product_id     = $request->input('product_id_'.$i);
			$line->combination_id = $request->input('combination_id_'.$i);
			$line->reference      = $request->input('reference_'.$i);
			$line->name           = $request->input('name_'.$i);
			$line->quantity       = $request->input('quantity_'.$i);

			$line->cost_price          = $request->input('cost_price_'.$i);
			$line->unit_price          = $request->input('unit_price_'.$i);
			$line->unit_customer_price = $request->input('unit_customer_price_'.$i);

			$line->unit_final_price    = $request->input('unit_final_price_'.$i);

			$line->unit_net_price   = $request->input('unit_final_price_'.$i)*(1.0 - $request->input('discount_percent_'.$i)/100.0);
			
			$line->discount_percent = $request->input('discount_percent_'.$i);
			$line->discount_amount_tax_incl = $request->input('discount_amount_tax_incl_'.$i, 0.0);
			$line->discount_amount_tax_excl = $request->input('discount_amount_tax_excl_'.$i, 0.0);

			$line->total_tax_incl = $request->input('total_tax_incl_'.$i);
			$line->total_tax_excl = $request->input('total_tax_excl_'.$i);

			$line->tax_percent = $request->input('tax_percent_'.$i);
			$line->commission_percent = $request->input('commission_percent_'.$i);

			$line->notes = $request->input('notes_'.$i);
			$line->locked = 0;
			
			$line->tax_id = $request->input('tax_id_'.$i);
			if ($this->customerInvoice->sales_rep_id > 0) {

		            $line->sales_rep_id = $this->customerInvoice->sales_rep_id;
		            $line->commission_percent = $salesrep->commission_percent;

		    } else {

					$line->sales_rep_id = 0;
		            $line->commission_percent = 0.0;
		    }


			$customerInvoice->CustomerInvoiceLines()->save($line);
		}


		return redirect('customerinvoices')
				->with('info', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $customerInvoice->id], 'layouts'));
	}

	/**
	 * Display the specified customerinvoice.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$cinvoice = $this->customerInvoice
							->with('customer')
							->with('invoicingAddress')
							->with('customerInvoiceLines')
							->with('currency')
							->findOrFail($id);

		$company = \App\Company::find( intval(Configuration::get('DEF_COMPANY')) );

		return View::make('customer_invoices.show', compact('cinvoice', 'company'));
	}

	/**
	 * Show the form for editing the specified customerinvoice.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$invoice = $this->customerInvoice
							->with('customer')
							->with('invoicingAddress')
							->with('customerInvoiceLines')
							->with('currency')
							->findOrFail($id);

		$customer = \App\Customer::find( $invoice->customer_id );

		$aBook       = $customer->addresses;

		$team = $customer->invoicing_address_id;
		$invoicing_address = $aBook->filter(function($item) use ($team) {	// Filter returns a collection!
		    return $item->id == $team;
		})->first();

		$addressbookList = array();
		foreach ($aBook as $address) {
			$addressbookList[$address->id] = $address->alias;
		}

		$invoice->document_date_form   = abi_date_short( $invoice->document_date );
		$invoice->delivery_date_form   = abi_date_short( $invoice->delivery_date );

		return View::make('customer_invoices.edit', compact('customer', 'invoicing_address', 'aBook', 'addressbookList', 'invoice'));
	}

	/**
	 * Update the specified customerinvoice in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$customerInvoice = $this->customerInvoice->findOrFail($id);
		
		// STEP 1 : validate data

		// Check Shipping Address
		if ( $request->input('shipping_address_id') < 1 ) 
			$request->replace( array('shipping_address_id' => $request->input('invoicing_address_id')) );

		// Open Balance ¿?¿?¿?
		$request->merge( array('open_balance' => floatval($request->input('total_tax_incl')) - floatval($request->input('down_payment')) ) );

		$this->validate($request, CustomerInvoice::$rules);

		// STEP 2 : build objects
		
		if ( !$request->input('draft') ) {
			$seq = \App\Sequence::find( $request->input('sequence_id') );
			$doc_id = $seq->getNextDocumentId();
			$extradata = [	'document_prefix'      => $seq->prefix,
							'document_id'          => $doc_id,
							'document_reference'   => $seq->getDocumentReference($doc_id),
						 ];
			$request->merge( $extradata );
		}

		$dates = [
						'document_date' => \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $request->input('document_date_form') ),
						'delivery_date' => \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $request->input('delivery_date_form') ),
				 ];
		$request->merge( $dates );

		$totals = [
						'total_discounts_tax_incl' => $request->input('order_gross_tax_incl') - $request->input('order_total_tax_incl'),
						'total_discounts_tax_excl' => $request->input('order_gross_tax_excl') - $request->input('order_total_tax_excl'),
						'total_products_tax_incl' => $request->input('order_gross_tax_incl'),
						'total_products_tax_excl' => $request->input('order_gross_tax_excl'),
						'total_tax_incl' => $request->input('order_total_tax_incl'),
						'total_tax_excl' => $request->input('order_total_tax_excl'),
				  ];
		$request->merge( $totals );

		// ToDo: Calculate 'commission_amount' (maybe after line saving!)

		$customerInvoice->update($request->all());


		// 
		// Lines stuff
		// 

		// STEP 1 : Delete current lines

		foreach( $customerInvoice->customerInvoiceLines as $line)
		{
			$line->delete();
		}

		// STEP 2 : Create new lines

		$line = $this->customerInvoiceLine;

		// Loop...
		$n = intval($request->input('nbrlines'));

        for($i = 0; $i < $n; $i++)
        {
			if ( !$request->has('lineid_'.$i) ) continue;	// Line was deleted on View

			// $line = new CustomerInvoiceLine();
			$line = $this->customerInvoiceLine;

			$line->line_sort_order = $request->input('line_sort_order_'.$i);
			$line->line_type       = $request->input('line_type_'.$i);

			$line->product_id     = $request->input('product_id_'.$i);
			$line->combination_id = $request->input('combination_id_'.$i);
			$line->reference      = $request->input('reference_'.$i);
			$line->name           = $request->input('name_'.$i);
			$line->quantity       = $request->input('quantity_'.$i);

			$line->cost_price          = $request->input('cost_price_'.$i);
			$line->unit_price          = $request->input('unit_price_'.$i);
			$line->unit_customer_price = $request->input('unit_customer_price_'.$i);

			$line->unit_final_price    = $request->input('unit_final_price_'.$i);

			$line->unit_net_price   = $request->input('unit_final_price_'.$i)*(1.0 - $request->input('discount_percent_'.$i)/100.0);
			
			$line->discount_percent = $request->input('discount_percent_'.$i);
			$line->discount_amount_tax_incl = $request->input('discount_amount_tax_incl_'.$i, 0.0);
			$line->discount_amount_tax_excl = $request->input('discount_amount_tax_excl_'.$i, 0.0);

			$line->total_tax_incl = $request->input('total_tax_incl_'.$i);
			$line->total_tax_excl = $request->input('total_tax_excl_'.$i);

			$line->tax_percent = $request->input('tax_percent_'.$i);
			$line->commission_percent = $request->input('commission_percent_'.$i);

			$line->notes = $request->input('notes_'.$i);
			$line->locked = 0;
			
			$line->tax_id = $request->input('tax_id_'.$i);
			if ($this->customerInvoice->sales_rep_id > 0) {

		            $line->sales_rep_id = $this->customerInvoice->sales_rep_id;
		            $line->commission_percent = $salesrep->commission_percent;

		    } else {

					$line->sales_rep_id = 0;
		            $line->commission_percent = 0.0;
		    }


			$customerInvoice->CustomerInvoiceLines()->save($line);
		}





		// 
		// Vouchers stuff
		// 
		if ( !$customerInvoice->draft OR 1) {

			$ototal = \App\FP::money_amount( $customerInvoice->total_tax_incl, $customerInvoice->currency );
			$ptotal = 0;
			$pmethod = $customerInvoice->paymentmethod;
			$dlines = $pmethod->deadlines;
			$pday = $customerInvoice->customer->payment_day;
			// $base_date = \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $customerInvoice->document_date );
			$base_date = $customerInvoice->document_date;

			for($i = 0; $i < count($pmethod->deadlines); $i++)
        	{
        		$next_date = $base_date->copy()->addDays($dlines[$i]['slot']);
        		// Calculate installment due date
        		$day   = $next_date->day;
        		$month = $next_date->month;
        		$year  = $next_date->year;

        		if ( $pday AND ($day != $pday) ) {

        			if ( $day > $pday) {

        				if ($month == 12) {

        					$month = 1;
        					$year += 1;

        				} else {

        					$month += 1;

        				}

        			}

        			$day = $pday;

        		}

        		$due_date = \Carbon\Carbon::createFromDate($year, $month, $day);

        		// Check Saturday & Sunday
        		if ( $due_date->dayOfWeek == 6 ) $due_date->addDays(2);
        		if ( $due_date->dayOfWeek == 0 ) $due_date->addDays(1);

        		if ( $i != (count($pmethod->deadlines)-1) ) {
        			$installment = \App\FP::money_amount( $ototal * $dlines[$i]['percentage'] / 100.0, $customerInvoice->currency );
        			$ptotal += $installment;
        		} else {
        			$installment = $ototal - $ptotal;	// Last Installment
        		}

        		// Create Voucher
        		$data = [	'reference' => null, 
        					'name' => ($i+1) . ' / ' . count($pmethod->deadlines), 
//        					'due_date' => \App\FP::date_short( \Carbon\Carbon::parse( $due_date ), \App\Context::getContext()->language->date_format_lite ), 
        					'due_date' => abi_date_short( \Carbon\Carbon::parse( $due_date ) ), 
        					'payment_date' => null, 
                            'amount' => $installment, 
                            'currency_conversion_rate' => $customerInvoice->currency_conversion_rate, 
                            'status' => 'pending', 
                            'notes' => null,
                        ];

                $payment = \App\Payment::create( $data );

                $payment->currency_id = $customerInvoice->currency_id;
                $payment->invoice_id = $customerInvoice->id;
                $payment->model_name = 'CustomerInvoice';
                $payment->owner_id = $customerInvoice->customer->id;
                $payment->owner_model_name = 'Customer';

                $payment->save();

                // ToDo: update Invoice next due date
        	}
			
		}




		

		return redirect('customerinvoices')
				->with('info', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $customerInvoice->id], 'layouts'));
	}

	/**
	 * Remove the specified customerinvoice from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->customerInvoice->findOrFail($id)->delete();

        return redirect('customerinvoices')
				->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
	}




    /*
    |--------------------------------------------------------------------------
    | Not CRUD stuff here
    |--------------------------------------------------------------------------
    */

	protected function invoice2pdf($id)
	{
		// PDF stuff
		try {
			$cinvoice = CustomerInvoice::
							  with('customer')
							->with('invoicingAddress')
							->with('customerInvoiceLines')
							->with('currency')
							->with('template')
							->findOrFail($id);

        } catch(ModelNotFoundException $e) {

            return Redirect::route('customers.index')
                     ->with('error', 'La Factura de Cliente id='.$id.' no existe.');
            // return Redirect::to('invoice')->with('message', trans('invoice.access_denied'));
        }

		$company = Company::find( intval(Configuration::get('DEF_COMPANY')) );

		$template = 'customer_invoices.templates.' . $cinvoice->template->file_name;
		$paper = $cinvoice->template->paper;	// A4, letter
		$orientation = $cinvoice->template->orientation;	// 'portrait' or 'landscape'.
		
		$pdf 		= PDF::loadView( $template, compact('cinvoice', 'company') )
							->setPaper( $paper )
							->setOrientation( $orientation );
		// PDF stuff ENDS
		
		return 	$pdf;
	}

	public function showpdf($id, Request $request)
	{
		// PDF stuff
		try {
			$cinvoice = CustomerInvoice::
							  with('customer')
							->with('invoicingAddress')
							->with('customerInvoiceLines')
							->with('currency')
							->with('template')
							->findOrFail($id);

        } catch(ModelNotFoundException $e) {

            return Redirect::route('customers.index')
                     ->with('error', 'La Factura de Cliente id='.$id.' no existe.');
            // return Redirect::to('invoice')->with('message', trans('invoice.access_denied'));
        }

		$company = \App\Company::find( intval(Configuration::get('DEF_COMPANY')) );

		$template = 'customer_invoices.templates.' . $cinvoice->template->file_name;
		$paper = $cinvoice->template->paper;	// A4, letter
		$orientation = $cinvoice->template->orientation;	// 'portrait' or 'landscape'.
		
		$pdf 		= \PDF::loadView( $template, compact('cinvoice', 'company') )
							->setPaper( $paper )
							->setOrientation( $orientation );
		// PDF stuff ENDS

		$pdfName	= 'invoice_' . $cinvoice->secure_key . '_' . $cinvoice->document_date;

		if ($request->has('screen')) return View::make($template, compact('cinvoice', 'company'));
		
		return 	$pdf->download( $pdfName . '.pdf');
	}

	public function sendemail( Request $request )
	{
		$id = $request->input('invoice_id');

		// PDF stuff
		try {
			$cinvoice = CustomerInvoice::
							  with('customer')
							->with('invoicingAddress')
							->with('customerInvoiceLines')
							->with('currency')
							->with('template')
							->findOrFail($id);

        } catch(ModelNotFoundException $e) {

            return Redirect::route('customers.index')
                     ->with('error', 'La Factura de Cliente id='.$id.' no existe.');
            // return Redirect::to('invoice')->with('message', trans('invoice.access_denied'));
        }

		$company = \App\Company::find( intval(Configuration::get('DEF_COMPANY')) );

		$template = 'customer_invoices.templates.' . $cinvoice->template->file_name;
		$paper = $cinvoice->template->paper;	// A4, letter
		$orientation = $cinvoice->template->orientation;	// 'portrait' or 'landscape'.
		
		$pdf 		= \PDF::loadView( $template, compact('cinvoice', 'company') )
							->setPaper( $paper )
							->setOrientation( $orientation );
		// PDF stuff ENDS

		$pathToFile 	= storage_path() . '/pdf/' . 'invoice_' . $cinvoice->secure_key . '_' . $cinvoice->document_date .'.pdf';
		$pdf->save($pathToFile);

		$template_vars = array(
			'invoice_num'   => $cinvoice->document_id > 0
									? $cinvoice->document_prefix . ' ' . $cinvoice->document_id
									: 'BORRADOR' ,
			'invoice_date'  => $cinvoice->document_date,
			'invoice_total' => $cinvoice->total_tax_incl, $cinvoice->currency,
			'custom_body'   => $request->input('email_body'),
			);

		$data = array(
			'from'     => $company->address->email,
			'fromName' => $company->name_fiscal,
			'to'       => $cinvoice->customer->address->email,
			'toName'   => $cinvoice->customer->name_fiscal,
			'subject'  => $request->input('email_subject'),
			);

		// http://belardesign.com/2013/09/11/how-to-smtp-for-mailing-in-laravel/
		\Mail::send('emails.customerinvoice.default', $template_vars, function($message) use ($data, $pathToFile)
		{
			$message->from($data['from'], $data['fromName']);

			$message->to( $data['to'], $data['toName'] )->bcc( $data['from'] )->subject( $data['subject'] );	// Will send blind copy to sender!
			
			$message->attach($pathToFile);

		});	
		
		unlink($pathToFile);
		

		return redirect()->back()->with('success', 'La Factura '.$cinvoice->document_prefix . ' ' . $cinvoice->document_id.' se envió correctamente al Cliente');
	}


/* ********************************************************************************************* */    


    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxLineSearch(Request $request)
    {
        // Request data
        $line_id         = $request->input('line_id');
        $product_id      = $request->input('product_id');
        $combination_id  = $request->input('combination_id', 0);
        $customer_id     = $request->input('customer_id');
        $sales_rep_id    = $request->input('sales_rep_id', 0);
        $currency_id     = $request->input('currency_id', \App\Context::getContext()->currency->id);

//        return "$product_id, $combination_id, $customer_id, $currency_id";

        if ($combination_id>0) {
        	$combination = \App\Combination::with('product')->with('product.tax')->find(intval($combination_id));
        	$product = $combination->product;
        	$product->reference = $combination->reference;
        	$product->name = $product->name.' | '.$combination->name;
        } else {
        	$product = \App\Product::with('tax')->find(intval($product_id));
        }

        $customer = \App\Customer::find(intval($customer_id));

        $sales_rep = null;
        if ($sales_rep_id>0)
        	$sales_rep = \App\SalesRep::find(intval($sales_rep_id));
        if (!$sales_rep)
        	$sales_rep = (object) ['id' => 0, 'commission_percent' => 0.0]; 
        
        $currency = ($currency_id == \App\Context::getContext()->currency->id) ?
                    \App\Context::getContext()->currency :
                    \App\Currency::find(intval($currency_id));

        $currency->conversion_rate = $request->input('conversion_rate', $currency->conversion_rate);

        if ( !$product || !$customer || !$currency ) {
            // Die silently
            return '';
        }

        $tax = $product->tax;

        // Calculate price per $customer_id now!
        $price = $product->getPriceByCustomer( $customer, $currency );
        $tax_percent = $tax->percent;
        $price->applyTaxPercent( $tax_percent );

        $data = [
//			'id' => '',
			'line_sort_order' => '',
			'line_type' => 'product',
			'product_id' => $product->id,
			'combination_id' => $combination_id,
			'reference' => $product->reference,
			'name' => $product->name,
			'quantity' => 1,
			'cost_price' => $product->cost_price,
			'unit_price' => $product->price,
			'unit_customer_price' => $price->getPrice(),
			'unit_final_price' => $price->getPrice(),
			'unit_final_price_tax_inc' => $price->getPriceWithTax(),
			'unit_net_price' => $price->getPrice(),
			'sales_equalization' => $customer->sales_equalization,
			'discount_percent' => 0.0,
			'discount_amount_tax_incl' => 0.0,
			'discount_amount_tax_excl' => 0.0,
			'total_tax_incl' => 0.0,
			'total_tax_excl' => 0.0,
			'tax_percent' => $product->as_percentable($tax_percent),
			'commission_percent' => $sales_rep->commission_percent,
			'notes' => '',
			'locked' => 0,
//			'customer_invoice_id' => '',
			'tax_id' => $product->tax_id,
			'sales_rep_id' => $sales_rep->id,
        ];

        $line = new CustomerInvoiceLine( $data );

        return view('customer_invoices._invoice_line', [ 'i' => $line_id, 'line' => $line ] );
    }


    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function ajaxLineOtherSearch(Request $request)
    {
        // Request data
        $line_id         = $request->input('line_id');
        $other_json      = $request->input('other_json');
        $customer_id     = $request->input('customer_id');
        $sales_rep_id    = $request->input('sales_rep_id', 0);
        $currency_id     = $request->input('currency_id', \App\Context::getContext()->currency->id);

//        return "$product_id, $combination_id, $customer_id, $currency_id";

        if ($other_json) {
        	$product = (object) json_decode( $other_json, true);
        } else {
        	$product = $other_json;
        }

        $customer = \App\Customer::find(intval($customer_id));

        $sales_rep = null;
        if ($sales_rep_id>0)
        	$sales_rep = \App\SalesRep::find(intval($sales_rep_id));
        if (!$sales_rep)
        	$sales_rep = (object) ['id' => 0, 'commission_percent' => 0.0]; 
        
        $currency = ($currency_id == \App\Context::getContext()->currency->id) ?
                    \App\Context::getContext()->currency :
                    \App\Currency::find(intval($currency_id));

        $currency->conversion_rate = $request->input('conversion_rate', $currency->conversion_rate);

        if ( !$product || !$customer || !$currency ) {
            // Die silently
            return '';
        }

        $tax = \App\Tax::find($product->tax_id);

        // Calculate price per $customer_id now!
        $amount_is_tax_inc = \App\Configuration::get('PRICES_ENTERED_WITH_TAX');
        $amount = $amount_is_tax_inc ? $product->price_tax_inc : $product->price;
        $price = new \App\Price( $amount, $amount_is_tax_inc, $currency );
        $tax_percent = $tax->percent;
        $price->applyTaxPercent( $tax_percent );

        $data = [
//			'id' => '',
			'line_sort_order' => '',
			'line_type' => $product->line_type,
			'product_id' => 0,
			'combination_id' => 0,
			'reference' => CustomerInvoiceLine::getTypeList()[$product->line_type],
			'name' => $product->name,
			'quantity' => 1,
			'cost_price' => $product->cost_price,
			'unit_price' => $product->price,
			'unit_customer_price' => $price->getPrice(),
			'unit_final_price' => $price->getPrice(),
			'unit_final_price_tax_inc' => $price->getPriceWithTax(),
			'unit_net_price' => $price->getPrice(),
			'sales_equalization' => $customer->sales_equalization,
			'discount_percent' => 0.0,
			'discount_amount_tax_incl' => 0.0,
			'discount_amount_tax_excl' => 0.0,
			'total_tax_incl' => 0.0,
			'total_tax_excl' => 0.0,
			'tax_percent' => $price->as_percentable($tax_percent),
			'commission_percent' => $sales_rep->commission_percent,
			'notes' => '',
			'locked' => 0,
//			'customer_invoice_id' => '',
			'tax_id' => $product->tax_id,
			'sales_rep_id' => $sales_rep->id,
        ];

        $line = new CustomerInvoiceLine( $data );

        return view('customer_invoices._invoice_line', [ 'i' => $line_id, 'line' => $line ] );
    }

}
