<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
	
    protected $fillable = [ 'name', 'iso_code', 'iso_code_num', 
                            'sign', 'signPlacement',  'blank',
                            'thousandsSeparator', 'decimalSeparator', 'decimalPlaces', 
                            'currency_conversion_rate', 'active'
                          ];

    public static $rules = array(
    	'name'    => array('required', 'min:2', 'max:32'),
    	'iso_code' => array('required'),
    	'iso_code_num' => array('required', 'min:1'),
        'decimalSeparator' => array('required'),
        'decimalPlaces' => array('required', 'min:0'),
    	'currency_conversion_rate' => array('required', 'min:0'),
    	);

    public function getFormatAttribute()
    {
        $format = '';
        $decimalSeparator = $this->decimalPlaces > 0 ?
                            $this->decimalSeparator : '';

        $format = 'XX'.$this->thousandsSeparator.'XXX'.$decimalSeparator.str_repeat('X',$this->decimalPlaces);

        $blank = $this->blank ? ' ' : '';
        if ( $this->signPlacement > 0 )
        	$format = $format . $blank . $this->sign;
        else
        	$format = $this->sign . $blank . $format;

        return $format;
    }
    

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function customerinvoices()
    {
        return $this->hasMany('App\Customerinvoice');
    }
}