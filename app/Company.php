<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {

	protected $fillable = ['name_fiscal', 'name_commercial', 'identification', 'website', 'notes'];
	
//    protected $guarded = array('id', 'address_id', 'currency_id');

    // Add your validation rules here
    public static $rules = array(
    	'name_fiscal' => array('required', 'min:2', 'max:128'),
        'website'     => 'url',
        'address_id'  => 'exists:addresses,id',
        'currency_id' => 'exists:currencies,id',
    	);


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
 
/*   
    public function addresses()
    {
        return $this->hasMany('App\Address', 'owner_id')->where('model_name', '=', 'Company');
    }
*/

    public function address()
    {
        // return $this->hasOne('App\Address', 'owner_id')->where('model_name', '=', 'Company');
        return $this->belongsTo('App\Address')->where('model_name', '=', 'Company');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }
}