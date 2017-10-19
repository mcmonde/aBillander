<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model {

    public static $statuses = array(
            'draft', 
            'pending', 
            'halfpaid', 
            'paid', 
            'doubtful',
        );

    protected $guarded = array('id');

    protected $dates = [
    					'document_date', 
    					'valid_until_date', 
    					'delivery_date', 
    					'delivery_date_real', 
                        'next_due_date', 
    					'edocument_sent_at', 
    					'posted_at'
    					];

    protected $fillable =  ['sequence_id', 'customer_id', 
                            'reference', 'document_discount', 'document_date', 'delivery_date', 'edocument_sent_at', 
//                            'document_prefix', 'document_id', 'document_reference', // These are calculated!!!
//                            'open_balance', 
                            'number_of_packages', 'shipping_conditions', 'tracking_number', 'currency_conversion_rate', 'down_payment', 
//                            'total_discounts_tax_incl', 'total_discounts_tax_excl', 'total_products_tax_incl', 'total_products_tax_excl', 
//                            'total_shipping_tax_incl', 'total_shipping_tax_excl', 'total_other_tax_incl', 'total_other_tax_excl', 
//                            'total_tax_incl', 'total_tax_excl', 'commission_amount', 
                            'notes', 'status', 
//                            'einvoice', 'printed', 'customer_viewed', 'posted', 
                            'invoicing_address_id', 'shipping_address_id', 'warehouse_id', 'carrier_id', 
                            'sales_rep_id', 'currency_id', 'payment_method_id', 'template_id', 
//                            'parent_document_id'
                            ];

	// Add your validation rules here
	public static $rules = [
                            'document_date' => 'date',
                            'delivery_date' => 'date',
                            'sequence_id' => 'exists:sequences,id',
                            'warehouse_id' => 'exists:warehouses,id',
                            'currency_id' => 'exists:currencies,id',
                            'payment_method_id' => 'exists:payment_methods,id',
	];

    public static function boot()
    {
        parent::boot();

        static::creating(function($cinvoice)
        {
            $cinvoice->secure_key = md5(uniqid(rand(), true));
        });

        static::deleting(function ($cinvoice)    // https://laracasts.com/discuss/channels/general-discussion/deleting-related-models
        {
            // before delete() method call this
            foreach($cinvoice->customerInvoiceLines as $line) {
                $line->delete();
            }
        });
    }

    public static function getStatusList()
    {
            $list = [];
            foreach (self::$statuses as $status) {
                $list[$status] = l($status, [], 'appmultilang');;
            }

            return $list;
    }

    public function getEditableAttribute()
    {
        return $this->status == 'draft';
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

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
        return $this->belongsTo('App\Template');
    }

    public function invoicingaddress()
    {
        return $this->belongsTo('App\Address', 'invoicing_address_id')->withTrashed();
    }

    public function shippingaddress()
    {
        return $this->belongsTo('App\Address', 'shipping_address_id')->withTrashed();
    }

    
    public function customerinvoicelines()      // http://advancedlaravel.com/eloquent-relationships-examples
    {
        return $this->hasMany('App\CustomerInvoiceLine', 'customer_invoice_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment', 'invoice_id')->where('model_name', '=', 'CustomerInvoice');
    }
/*    
    public function addresses()
    {
        return $this->hasMany('Address', 'owner_id')->where('model_name', '=', 'Customer');
    }
*/
}