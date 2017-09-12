<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRule extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [ 'country', 'state', 'sales_equalization', 'name', 'percent', 'amount', 'position' ];

    public static $rules = array(
    	'name'     => array('required'),
        'percent'  => array('numeric', 'between:0,100'), 
        'amount'   => array('numeric'),
        'position' => array('numeric'),      // , 'min:0')   Allow negative in case starts on 0
    	);

    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tax()
    {
        return $this->belongsTo('App\Tax', 'tax_id');
	}
}