<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {
    
    public static $company_path = '/uploads/company/';
    protected static $access_rights = 0775;

	protected $fillable = ['name_fiscal', 'name_commercial', 'identification', 'apply_RE', 
                            'website', 'company_logo','notes', 'currency_id'];
	
//    protected $guarded = array('id', 'address_id', 'currency_id');

    // Add your validation rules here
    public static $rules = array(
    	'name_fiscal' => array('required', 'min:2', 'max:128'),
        'website'     => 'nullable|url',
        'currency_id' => 'exists:currencies,id',
    	);


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function address()
    {
        // return $this->morphMany('App\Address', 'addressable')->first();
        // See: https://stackoverflow.com/questions/22012877/laravel-eloquent-polymorphic-one-to-one
        // https://laracasts.com/discuss/channels/general-discussion/one-to-one-polymorphic-inverse-relationship-with-existing-database
        return $this->hasOne('App\Address', 'addressable_id','id')
                   ->where('addressable_type', 'App\Company');
    }
    
    public function addresses()
    {
        return $this->morphMany('App\Address', 'addressable');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }
}