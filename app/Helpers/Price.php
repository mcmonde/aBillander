<?php 

namespace App;

use App\Traits\ViewFormatterTrait;

use \App\Currency as Currency;

class Price {

    use ViewFormatterTrait;
	
    public function __construct( $amount, $amount_is_tax_inc = 0, Currency $currency = null, $currency_conversion_rate = null )
    {
        if ( $currency === null ) $currency = \App\Context::getContext()->currency;
        if ( $currency_conversion_rate === null ) $currency_conversion_rate = $currency->conversion_rate;

        $this->amount = $amount;
        $this->price_is_tax_inc = $amount_is_tax_inc;
        $this->tax_percent = null;
        $this->currency = $currency;
        $this->currency_conversion_rate = $currency_conversion_rate;

        if ($amount_is_tax_inc) {
            $this->price = null;
            $this->price_tax_inc = $amount;
        } else {
            $this->price = $amount;
            $this->price_tax_inc = null;
        }

        $this->price_list_id      = 0;
        $this->price_list_line_id = 0;
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

        if ( ($currency_from->id == $currency_to->id) && ($currency_from->conversion_rate == $currency_to->conversion_rate) ) return clone $this;

        $amount = Currency::convertPrice($this->amount, $currency_from, $currency_to);

        $priceObject = new Price( $amount, $this->price_is_tax_inc, $currency, $currency_conversion_rate);

        if ($this->tax_percent !== null) $priceObject->applyTaxPercent( $this->tax_percent );

        $priceObject->price_list_id      = $this->price_list_id;
        $priceObject->price_list_line_id = $this->price_list_line_id;

        return $priceObject;
    }

    public function convertToBaseCurrency()
    {
        return $this->convert( \App\Context::getContext()->currency );
    }

    public function applyTaxPercent( $percent = null )
    {
        if ( $percent === null ) $percent = 0.0;

        if ($this->price_is_tax_inc) {
            $this->price = $this->price_tax_inc/(1.0+$percent/100.0);
        } else {
            $this->price_tax_inc = $this->price*(1.0+$percent/100.0);
        }

        $this->tax_percent = $percent;
    }
    
    public function getPrice()
    {
        if ($this->price_is_tax_inc) {
            if ( $this->tax_percent == null ) return null;
            else return $this->price;
        } else {
            return $this->price;
        }
    }
    
    public function getPriceWithTax()
    {
        if ($this->price_is_tax_inc) {
            return $this->price_tax_inc;
        } else {
            if ( $this->tax_percent == null ) return null;
            else return $this->price_tax_inc;
        }
    }
    
    public function getDiscount( \App\Price $priceObj)
    {
        //
    }
    
    public function getMargin( \App\Price $priceObj)
    {
        //
    }
	
}