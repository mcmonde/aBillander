<?php namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ViewFormatterTrait;

class PriceList extends Model {

    use ViewFormatterTrait;
    //    use SoftDeletes;

//    protected $dates = ['deleted_at'];

	protected $fillable = ['name', 'type', 'price_is_tax_inc', 'amount', 'currency_id'];

	public static $rules = array(
                                    'name' => 'required',
                                    'currency_id' => 'exists:currencies,id'
                                );

    /**
     * Handy method
     * 
     */
    public function getFeatures()
    {
        $features = ' &nbsp; &nbsp;';

        $features .=  $this->getType() . ' ' . getExtra();

        return $features;
    }

    public function getType()
    {
        $features = '';

        $features .=  $this->type == 0 ? l('Fixed price', [], 'appmultilang') : 
                    ($this->type == 1 ? l('Discount percentage', [], 'appmultilang') : 
                    ($this->type == 2 ? l('Margin percentage', [], 'appmultilang') : ''))
        ;

        return $features;
    }

    public function getExtra()
    {
        $features = '';

        if ($this->type == 0){
            $features .=  $this->price_is_tax_inc ? l('Tax Included', [], 'pricelists') : '';
        } else {
            $features .=  ' ('.$this->as_percent( 'amount' ).'%) ';
        }

        return $features;
    }

    public function calculatePrice( \App\Product $product )
    {
        switch ($this->type) {
            // Discount percentage
            case 1:
                $price = $product->price*(1.0-($this->amount/100.0));
                break;
            // Margin percentage
            case 2:
                $price = \App\Calculator::price($product->cost_price, $this->amount);
                break;
            // Fixed price
            case 0:
            default:
                $price = $this->price_is_tax_inc
                         ? $product->price*(1.0+($product->tax->percent/100.0))
                         : $product->price;
                break;
        }

        // Convert to Price List Currency
        $currency = \App\Currency::find( $this->currency_id );

        if ( !$currency ) 
            $currency = \App\Currency::find( intval(Configuration::get('DEF_CURRENCY')) );

        $price *= $currency->conversion_rate;

        return $price;
    }

    // Deprecated 
    public static function priceCalculator( \App\PriceList $plist = null, \App\Product $product )
    {
        if (!$plist) return false;

        // $plist = \App\PriceList::findOrFail($list_id);

        switch ($plist->type) {
            case 1:
                $price = $product->price*(1.0-($plist->amount/100.0));
                break;
            case 2:
                $price = \App\Calculator::price($product->cost_price, $plist->amount);
                break;
            case 0:
            default:
                $price = $plist->price_is_tax_inc
                         ? $product->price*(1.0+($product->tax->percent/100.0))
                         : $product->price;
                break;
        }

        return (-1.0)*$price;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function pricelistlines() 
    {
        return $this->hasMany('App\PriceListLine');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }
    
}