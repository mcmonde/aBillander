<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\TaxRule as TaxRule;

class Tax extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [ 'name', 'active' ];

    public static $rules = array(
    	'name'    => array('required', 'min:2', 'max:64'),
 //   	'percent' => array('required', 'numeric', 'between:0,100')
    	);
    
    
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPercentAttribute($v)
    {
        // Address / Company models need fixing to retrieve country ISO code
        // $country = Context::getContext()->company->address()->country_ISO;
        $country = 'ES';

        $value = TaxRule::where('country', '=', '')->orWhere('country', '=', $country)->orderBy('position', 'asc')->first()->percent;

        return $value;
    }


    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */
    
    public function getFirstRule()
    {
        return TaxRule::where('tax_id', '=', $this->id)->orderBy('position', 'asc')->first();
    }
	
    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function taxrules()
    {
        return $this->hasMany('App\TaxRule', 'tax_id')->orderby('position', 'asc');
    }
    
    public function products()
    {
        return $this->hasMany('App\Product');
    }
	
}