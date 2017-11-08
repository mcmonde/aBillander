<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ViewFormatterTrait;

class Customer extends Model {

    use ViewFormatterTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
	
    protected $fillable = ['name_fiscal', 'name_commercial', 'website', 'identification', 'webshop_id', 
                            'payment_days', 'no_payment_month', 
                           'outstanding_amount_allowed', 'unresolved_amount', 
                           'notes', 'sales_equalization', 'accept_einvoice', 'blocked', 'active', 
                           'sales_rep_id', 'currency_id', 'customer_group_id', 'payment_method_id', 'sequence_id', 
                           'invoice_template_id', 'carrier_id', 'price_list_id', 'direct_debit_account_id', 
                           'invoicing_address_id', 'shipping_address_id', 
                ];
    
    public static $rules = array(
        'name_fiscal' => 'required',
        'no_payment_month' => 'numeric|min:0|max:12',
    	);

    public static function boot()
    {
        parent::boot();

        static::creating(function($client)
        {
            $client->secure_key = md5(uniqid(rand(), true));
        });

        // cause a delete of a Customer to cascade to children so they are also deleted
        static::deleted(function($client)
        {
            $client->addresses()->delete(); // Not calling the events on each child : http://laravel.io/forum/03-26-2014-delete-relationschild-relations-without-cascade

            // See:
            // http://laravel-tricks.com/tricks/cascading-deletes-with-model-events
            // http://laravel-tricks.com/tricks/using-model-events-to-delete-related-items
            /*
                    // Attach event handler, on deleting of the user
                    User::deleting(function($user)
                    {   
                        // Delete all tricks that belong to this user
                        foreach ($user->tricks as $trick) {
                            $trick->delete();
                        }
                    });
            */
        });
    }

    // Get the full name of a User instance using Eloquent accessors
    
    public function getNameAttribute() 
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function currentpricelist()
    {
        // First: Customer has pricelist?
        if ($this->pricelist) {

            return $this->pricelist;
        } 

        // Second: Customer Group has pricelist?
        if ($this->customergroup AND $this->customergroup->pricelist) {

            return $this->customergroup->pricelist;
        }
    }
    
    public function paymentDays() 
    {
        if ( !trim($this->payment_day) ) return [];

        $dstr = str_replace( ';', ',', $this->payment_day );
        $days = array_map( 'intval', explode(',', $dstr) );

        sort( $days, SORT_NUMERIC );

        return $days;
    }

    /**
     * Adjust date to Customer Payment Days
     */
    public function paymentDate( \Carbon\Carbon $date ) 
    {
        $pdays = $this->paymentDays();
        $n = count($pdays);
        if ( !$n ) return $date;

        $day   = $date->day;
        $month = $date->month;
        $year  = $date->year;

        if ( $day > $pdays[$n-1] ) {

            $day = $pdays[0];

            if ($month == 12) {

                $month = 1;
                $year += 1;

            } else {

                $month += 1;

            }

        } else {

            foreach ($pdays as $pday) {
                if ($day <= $pday) {
                    $day = $pday;
                    break;
                }
            }

        }

        $payday = \Carbon\Carbon::createFromDate($year, $month, $day);

        // Check Saturday & Sunday
        if ( $payday->dayOfWeek == 6 ) $payday->addDays(2); // Saturday
        if ( $payday->dayOfWeek == 0 ) $payday->addDays(1); // Sunday

        return $payday;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all of the customer's Bank Accounts.
     */
    public function bankaccounts()
    {
        return $this->morphMany('App\BankAccount', 'bank_accountable');
    }
    
    public function addresses()
    {
        return $this->morphMany('App\Address', 'addressable');
    }


    public function getAddressList()
    {
        return $this->morphMany('App\Address', 'addressable')->pluck( 'alias', 'id' )->toArray();
    }


    public function address()
    {
        return $this->invoicing_address();
    }
    
    public function invoicing_address()
    {   
        return $this->belongsTo('App\Address', 'invoicing_address_id')
                   ->where('addresses.addressable_type', 'App\Customer');
    }
    
    public function shipping_address()
    {
        return $this->belongsTo('App\Address', 'shipping_address_id')
                   ->where('addresses.addressable_type', 'App\Customer');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function pricelist()
    {
        return $this->belongsTo('App\PriceList', 'price_list_id');
    }

    public function salesrep()
    {
        return $this->belongsTo('App\SalesRep', 'sales_rep_id');
    }

    public function customergroup()
    {
        return $this->belongsTo('App\CustomerGroup');
    }

    
    public function customerinvoices()
    {
        return $this->hasMany('App\CustomerInvoice');
    }
    
    public function payments()
    {
        // return $this->hasMany('App\Payment', 'owner_id')->where('payment.owner_model_name', '=', 'Customer');

        return $this->morphMany('App\Payment', 'paymentorable');
    }

    
    /*
    |--------------------------------------------------------------------------
    | Data Provider
    |--------------------------------------------------------------------------
    */

    /**
     * Provides a json encoded array of matching client names
     * @param  string $query
     * @return json
     */
    public static function searchByNameAutocomplete($query, $params)
    {
        $clients = Customer::select('name_fiscal', 'name_commercial', 'id')->orderBy('name_fiscal')->where('name_fiscal', 'like', '%' . $query . '%');
        if ( isset($params['name_commercial']) AND ($params['name_commercial'] == 1) )
            $clients = $clients->orWhere('name_commercial', 'like', '%' . $query . '%');
        
        $clients = $clients->get();

        $return = array();

        if ( isset($params['name_commercial']) AND ($params['name_commercial'] == 1) ) {
            foreach ($clients as $client) {
                $return[] = array ('value' => $client->name_fiscal.' - '.$client->name_commercial, 'data' => $client->id);
            }

        } else {
            foreach ($clients as $client) {
                $return[] = array ('value' => $client->name_fiscal, 'data' => $client->id);
            }
        }

        return json_encode( array('query' => $query, 'suggestions' => $return) );
    }
    

    /*
    |--------------------------------------------------------------------------
    | Price calculations
    |--------------------------------------------------------------------------
    */

    public function getPrice( \App\Product $product, \App\Currency $currency = null )
    {
        // Best price (if Price for $currency is not set)
        $best_price = null;

        if (!$currency && $this->currency_id)
            $currency = $this->currency;

        if (!$currency)
            $currency = \App\Context::getContext()->currency;

        // ToDo: Set special prices priorities
        // First: Product has special price for this Customer?

        // Second: Product has special price for this Customer's Customer Group?

        // Third: Customer has pricelist?
        if ($this->pricelist) {

            $price = $this->pricelist->getPrice( $product );

            if ($currency->id == $this->pricelist->currency_id) {
                return $price;
            }

            if ( $best_price == null ) $best_price = $price;
        } 

        // Fourth: Customer Group has pricelist?
        if ($this->customergroup && $this->customergroup->pricelist) {

            $price = $this->customergroup->pricelist->getPrice( $product );

            if ($currency->id == $this->customergroup->pricelist->currency_id) {
                return $price;
            }

            if ( $best_price == null ) $best_price = $price;
        }

        // Otherwise, use product price (initial or base price)
        $price = $product->getPrice();

        if ($currency->id == $price->currency->id) {
            return $price;
        }

        if ( $best_price == null ) $best_price = $price;

        // If you get here, no matching currency found. So, convert best price recorded
        $price = $best_price->convert( $currency );

        return $price;
    }

    public function getTaxRules( \App\Product $product )
    {
        $rules = collect([]);

        // Sales Equalization
        if ( $this->sales_equalization ) {

            $tax = $product->tax;


            $address = $this->invoicing_address;

            $country_id = $address->country_id;
            $state_id   = $address->state_id;

            $rules_re = $tax->taxrules()->where(function ($query) use ($country_id) {
                $query->where(  'country_id', '=', 0)
                      ->OrWhere('country_id', '=', $country_id);
            })
                                     ->where(function ($query) use ($state_id) {
                $query->where(  'state_id', '=', 0)
                      ->OrWhere('state_id', '=', $state_id);
            })
                                     ->where('rule_type', '=', 'sales_equalization')
                                     ->get();

            if ( $rules_re->isNotEmpty() ) $rules = $rules->merge( $rules_re );

        }

        return $rules;
    }
    
}
