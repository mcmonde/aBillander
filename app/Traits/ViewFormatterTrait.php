<?php 

namespace App\Traits;

trait ViewFormatterTrait
{
    public function quantity( $key = '' )
    {
        if ( !$key || !array_key_exists($key, $this->attributes) ) return null;

        // quantity should be float!!
        $data = floatval( $this->{$key} );

        // Do formatting
        // Get decimal places -> decimal_places model property
        $decimals = array_key_exists('decimal_places', $this->attributes) ?
        			$this->decimal_places :
        			intval( \App\Configuration::get('DEF_QUANTITY_DECIMALS') );
        
        $data = number_format($data, $decimals, '.', '');

        return $data;
    }

    public function price( $key = '', \App\Currency $currency = null )
    {
        return $this->money_amount( $key, $currency );
    }

    public static function money($amount = 0, \App\Currency $currency = null)
    {
        if (!$currency)
            $currency = Context::getContext()->currency;

        $number = number_format($amount, $currency->decimalPlaces, $currency->decimalSeparator, $currency->thousandsSeparator);

        $blank = $currency->blank ? ' ' : '';
        if ( $currency->signPlacement > 0 )
            $number = $number . $blank . $currency->sign;
        else
            $number = $currency->sign . $blank . $number;

        return $number;
    }

    public function money_amount( $key = '', \App\Currency $currency = null )
    {
        if ( !$key || !array_key_exists($key, $this->attributes) ) return null;

        $data = floatval( $this->{$key} );

        if (!$currency)
            $currency = \App\Context::getContext()->currency;

        $number = number_format($data, $currency->decimalPlaces, '.', '');

        return $number;
    }

    public function percent( $key = '', $decimalPlaces = null )
    {
        // abi_r($this->{$key}); 
        // abi_r( strlen($key) > 0  );
        // abi_r( array_key_exists($key.$key, $this->attributes), true );

        // if ( !$key || !\property_exists($this, $key) ) return null;
        if ( !$key || !array_key_exists($key, $this->attributes) ) return null;

        // quantity should be float!!
        $data = floatval( $this->{$key} ); // abi_r($data, true);

        if ( !$decimalPlaces ) $decimalPlaces = \App\Configuration::get('DEF_PERCENT_DECIMALS');

        // abi_r($decimalPlaces, true);

        
        $number = number_format($data, $decimalPlaces, '.', '');

        return $number;
    }

    public function date( $key = '' )
    {
        // 
    }

    public static function date_short(\Carbon\Carbon $date, $format = '')
    {
        // http://laravel.io/forum/03-11-2014-date-format
        // https://laracasts.com/forum/?p=764-saving-carbon-dates-from-user-input/0

        // if ($format == '') $format = \App\Configuration::get('DATE_FORMAT_SHORT');     
        if ($format == '') $format = \App\Context::getContext()->language->date_format_lite; // Should take value after User / Environment settings
        if (!$format) $format = \App\Configuration::get('DATE_FORMAT_SHORT');
        // echo ($format); die();
        // $date = \Carbon\Carbon::createFromFormat($format, $date);    
        // http://laravel.io/forum/03-12-2014-class-carbon-not-found?page=1

        // echo $date.' - '.Configuration::get('DATE_FORMAT_SHORT').' - '.$date->format($format); die();

        return $date->format($format);
    }
}