<?php 

namespace App\Traits;

trait ViewFormatterTrait
{
    public function as_quantity( $key = '' )
    {
        if ( !$key || !array_key_exists($key, $this->attributes) ) return null;

        // quantity should be float!!
        $data = floatval( $this->{$key} );

        // Do formatting
        // Get decimal places -> decimal_places model property
        $decimals = array_key_exists('quantity_decimal_places', $this->attributes) ?
        			$this->quantity_decimal_places :
        			intval( \App\Configuration::get('DEF_QUANTITY_DECIMALS') );
        
        $data = number_format($data, $decimals, '.', '');

        return $data;
    }

    public static function as_money( $key = '', \App\Currency $currency = null )
    {
        if ( !$key || !array_key_exists($key, $this->attributes) ) return null;

        $amount = floatval( $this->{$key} );

        return \App\Currency::viewPriceWithSign($amount, $currency);
    }

    public function as_money_amount( $key = '', \App\Currency $currency = null )
    {
        if ( !$key || !array_key_exists($key, $this->attributes) ) return null;

        $amount = floatval( $this->{$key} );

        return \App\Currency::viewPrice($amount, $currency);
    }

    public function as_price( $key = '', \App\Currency $currency = null )
    {
        return $this->as_money_amount( $key, $currency );
    }

    public function as_percent( $key = '', $decimalPlaces = null )
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

    public function as_percentable( $val = 0.0, $decimalPlaces = null )
    {
        // abi_r($this->{$key}); 
        // abi_r( strlen($key) > 0  );
        // abi_r( array_key_exists($key.$key, $this->attributes), true );

        // if ( !$key || !\property_exists($this, $key) ) return null;
        $data = floatval( $val ); // abi_r($data, true);

        if ( $decimalPlaces === null ) $decimalPlaces = \App\Configuration::get('DEF_PERCENT_DECIMALS');

        // abi_r($decimalPlaces, true);

        
        $number = number_format($data, $decimalPlaces, '.', '');

        return $number;
    }

    public function as_priceable( $val = 0.0, \App\Currency $currency = null )
    {
        $data = floatval( $val );

        if (!$currency)
            $currency = \App\Context::getContext()->currency;

        $number = number_format($data, $currency->decimalPlaces, '.', '');

        return $number;
    }
    public function as_quantityable( $val = 0.0, $decimalPlaces = null )
    {
        $data = floatval( $val );

        // Do formatting
        // Get decimal places -> decimal_places model property
        if ( $decimalPlaces === null ) {
            $decimals = array_key_exists('quantity_decimal_places', $this->attributes) ?
                        $this->quantity_decimal_places :
                        intval( \App\Configuration::get('DEF_QUANTITY_DECIMALS') );
        } else {
            $decimals = $decimalPlaces;
        }

        $data = number_format($data, $decimals, '.', '');

        return $data;
    }

    public function as_date( $key = '' )
    {
        // 
    }

    public static function as_date_short(\Carbon\Carbon $date, $format = '')
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