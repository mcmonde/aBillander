<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
	
    protected $guarded = array('id');

	// protected $fillable = [];

    // Add your validation rules here
    public static $rules = array(
//    	'name' => array('required', 'min:2', 'max:64', 'unique:taxes,name,{id}'),
//    	'name'    => array('required', 'min:2', 'max:64'),
//    	'percent' => array('required', 'numeric', 'between:0,100')
//        'email' => 'required|email',
        'name' => 'required',
//       'website' => 'url',
    	);

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function paymentmethod()
    {
        return $this->belongsTo('App\PaymentMethod', 'payment_method_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function template()
    {
        return $this->belongsTo('App\Template', 'invoice_template_id');
    }

    public function pricelist()
    {
        return $this->belongsTo('App\PriceList', 'price_list_id');
    }

    public function sequence()
    {
        return $this->belongsTo('App\Sequence');
    }

    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

 // Cuenta remesas
 //
 //   public function directDebitAccount()
 //   {
 //       return $this->belongsTo('BankAccount');
 //   }
}