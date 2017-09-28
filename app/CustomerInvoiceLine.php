<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoiceLine extends Model {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	// protected $fillable = [];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerinvoice()
    {
       return $this->belongsTo('CustomerInvoice', 'customer_invoice_id');
    }
    
    public function customerinvoicelinetaxes()
    {
        return $this->hasMany('App\CustomerInvoiceLineTax', 'customer_invoice_line_id');
    }

}