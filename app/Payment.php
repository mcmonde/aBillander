<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

 //   protected $guarded = array('id');

    protected $dates = [
    					'due_date', 
    					'payment_date'
    					];

    protected $fillable =  ['reference', 'name', 'due_date', 'payment_date', 
                            'amount', 'currency_conversion_rate', 'status', 
                            'notes'];

	// Add your validation rules here
	public static $rules = [
            'due_date' => 'required|date',
//            'payment_date' => 'date',
	];

    
    public function getDueDateAttribute($value)
    {
        // See: https://laracasts.com/discuss/channels/general-discussion/how-to-carbonparse-in-ddmmyyyy-format

        return \App\FP::date_short( \Carbon\Carbon::parse($value), \App\Context::getContext()->language->date_format_lite );
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $value );
    }
    
    public function getPaymentDateAttribute($value)
    {
        if ($value)
            return \App\FP::date_short( \Carbon\Carbon::parse($value), \App\Context::getContext()->language->date_format_lite );
        else
            return NULL;
    }

    public function setPaymentDateAttribute($value)
    {
        if ($value)
            $this->attributes['payment_date'] = \Carbon\Carbon::createFromFormat( \App\Context::getContext()->language->date_format_lite, $value );
        else
            $this->attributes['payment_date'] = NULL;
    }

    public function getAmountAttribute($value)
    {
        return \App\FP::money_amount( $value, $currency = null);
    }

//    public function setAmountAttribute($value)
//    {
//        $this->attributes['amount'] = ;
//    }



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerInvoice()
    {
        return $this->belongsTo('App\CustomerInvoice', 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'owner_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

}
