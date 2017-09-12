<?php 

namespace App\Traits;

trait ViewFormatterTrait
{
    public function quantity( $key = '' )
    {
        if ( !$key || !property_exists($this, $key) ) return null;

        // quantity should be float!!
        $data = floatval( $this->{$key} );

        // Do formatting
        // Get decimal places -> decimal_places model property
        $decimals = property_exists($this, 'decimal_places') ?
        			$this->decimal_places :
        			intval( \Configuration::get('DEF_QUANTITY_DECIMALS') );
        
        $data = number_format($data, $decimals, '.', '');

        return $data;
    }

    public function price( $key = '' )
    {
        // 
    }

    public function percent( $key = '' )
    {
        // 
    }

    public function date( $key = '' )
    {
        // 
    }
}