<?php 

namespace App;

use \App\Currency as Currency;

class Price {
	
    public function __construct($amount, $amount_is_tax_inc = 0, Currency $currency = null, $currency_conversion_rate = null )
    {
        if ( $currency === null ) $currency = \App\Context::getContext()->currency;
        if ( $currency_conversion_rate === null ) $currency_conversion_rate = $currency->conversion_rate;

        if ($amount_is_tax_inc) {
            $this->price = null;
            $this->price_tax_inc = $amount;
        } else {
            $this->price = $amount;
            $this->price_tax_inc = null;
        }
        $this->amount = $amount;
        $this->price_is_tax_inc = $amount_is_tax_inc;
        $this->tax_percent = null;
        $this->currency = $currency;
        $this->currency_conversion_rate = $currency_conversion_rate;
    }
    
    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    public function convert( Currency $currency, $currency_conversion_rate = null )
    {
        $currency_from = $this->currency;
        $currency_from->conversion_rate = $this->currency_conversion_rate;

        $currency_to = $currency;
        if ( $currency_conversion_rate !== null ) $currency_to->conversion_rate = $currency_conversion_rate;

        $this->amount = Currency::convertPrice($amount, $currency_from, $currency_to);

        $priceObject = new Price( $amount, $this->price_is_tax_inc, $currency, $currency_conversion_rate);

        if ($this->tax_percent !== null) $priceObject->applyTaxPercent( $this->tax_percent );

        return $priceObject;
    }

    public function applyTaxPercent( $percent = null )
    {
        if ( $percent === null ) $percent = 0.0;

        if ($this->price_is_tax_inc) {
            $this->price = $this->price_tax_inc/(1.0+$percent/100.0);
        } else {
            $this->price_tax_inc = $this->price_tax_inc*(1.0+$percent/100.0);
        }

        $this->tax_percent = $percent;
    }
	
}