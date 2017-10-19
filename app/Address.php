<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [ 'alias', 'webshop_id', 'name_commercial', 
                            'address1', 'address2', 'postcode', 'city', 'state_id', 'country_id', 
                            'firstname', 'lastname', 'email', 
                            'phone', 'phone_mobile', 'fax', 'notes', 'active', 
                            'latitude', 'longitude',
                          ];

    public static $rules = array(
        'alias'    => 'required|min:2|max:32',
        'address1' => 'required|min:2|max:128',
        'state_id' => 'exists:states,id',           // If State exists, Country must do also!
        );

    public static function related_rules($rel = 'address')
    {
        // https://laracasts.com/discuss/channels/requests/laravel-5-validation-request-how-to-handle-validation-on-update/?page=1
        $rules = array();
        
        foreach ( self::$rules as $key => $rule) 
        {
            $rules[ $rel.'.'.$key ] = $rule;
        }
        return $rules;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
//        return $this->belongsTo('App\Customer', 'owner_id')->where('model_name', '=', 'Customer');
    }
    
    public function addressable()
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    public function state()
    {
        return $this->belongsTo('App\State');
    }
    

    /*
    |--------------------------------------------------------------------------
    | Tax calculations
    |--------------------------------------------------------------------------
    */

    public function getTaxRules( \App\Tax $tax )
    {
        $country_id = $this->country_id;
        $state_id   = $this->state_id;

        $rules = $tax->taxrules()->where(function ($query) use ($country_id) {
            $query->where(  'country_id', '=', 0)
                  ->OrWhere('country_id', '=', $country_id);
        })
                                 ->where(function ($query) use ($state_id) {
            $query->where(  'state_id', '=', 0)
                  ->OrWhere('state_id', '=', $state_id);
        })
                                 ->where('rule_type', '=', 'sales')
 //                                ->orderBy('position', 'asc')     // $tax->taxrules() is already sorted by position asc
                                 ->get();

        return $rules;
    }
    
}