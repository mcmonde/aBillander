<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
	
    protected $fillable = ['name_fiscal', 'name_commercial', 'website', 'identification', 'webshop_id', 
                           'outstanding_amount_allowed', 'unresolved_amount', 
                           'notes', 'sales_equalization', 'accept_einvoice', 'blocked', 'active', 
                           'sales_rep_id', 'currency_id', 'customer_group_id', 'payment_method_id', 'sequence_id', 
                           'invoice_template_id', 'carrier_id', 'price_list_id', 'direct_debit_account_id', 
                           'invoicing_address_id', 'shipping_address_id', 
                ];
    
    public static $rules = array(
        'name_fiscal' => 'required',
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
        return $this->hasMany('App\Address', 'owner_id')->where('model_name', '=', 'Customer');
    }

    public function address()
    {
        return $this->belongsTo('App\Address', 'invoicing_address_id')->where('addresses.model_name', '=', 'Customer');
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
        return $this->hasMany('App\Payment', 'owner_id')->where('payment.owner_model_name', '=', 'Customer');
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
        $clients = Customer::select('name_fiscal', 'id')->orderBy('name_fiscal')->where('name_fiscal', 'like', '%' . $query . '%');
        if ( isset($params['name_commercial']) AND ($params['name_commercial'] == 1) )
            $clients = $clients->orWhere('name_commercial', 'like', '%' . $query . '%');
        $clients = $clients->get();

        $return = array();

        foreach ($clients as $client)
        {
            // $return[]['value'] = $client->name_fiscal;
            $return[] = array ('value' => $client->name_fiscal, 'data' => $client->id);
        }

        return json_encode( array('query' => $query, 'suggestions' => $return) );
    }
	
}