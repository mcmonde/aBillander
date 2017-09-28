<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoiceLineTax extends Model {

	//


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerinvoiceline()
    {
       return $this->belongsTo('CustomerInvoiceLine', 'customer_invoice_line_id');
    }

}
