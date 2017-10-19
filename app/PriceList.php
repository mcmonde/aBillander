<?php 

namespace App;

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
                $price = $this->price_is_tax_inc
                         ? $product->price_tax_inc
                         : $product->price;
                $price = $price*(1.0-($this->amount/100.0));
                break;
            // Margin percentage
            case 2:
                $bprice = \App\Calculator::price($product->cost_price, $this->amount);
                $price = $this->price_is_tax_inc
                         ? $bprice*(1.0+($product->tax->percent/100.0))
                         : $bprice;
                break;
            // Fixed price
            case 0:
            default:
                $price = $this->price_is_tax_inc
                         ? $product->price_tax_inc
                         : $product->price;
                break;
        }

        // Convert to Price List Currency
        $currency = \App\Currency::find( $this->currency_id );

        if ( !$currency ) // Convention: No currency is defaut currency
            $currency = \App\Currency::find( intval(Configuration::get('DEF_CURRENCY')) );
        else
            $price *= $currency->conversion_rate;

        return $price;
    }

    public function getPrice( \App\Product $product )
    {
        $line = $this->pricelistlines()->where('product_id', '=', $product->id)->first();

        if ( !$line ) $line = $this->addLine( $product );

        $price = new \App\Price( $line->price, $this->price_is_tax_inc, $this->currency);

        return $price;
    }

    public function addLine( \App\Product $product, $price = null )
    {
        if ($price === null) $price = $this->calculatePrice( $product );

        $line = \App\PriceListLine::create( [ 'product_id' => $product->id, 'price' => $price ] );

        $this->pricelistlines()->save($line);

        return $line;
    }

    public function getLine( \App\Product $product )
    {
        $line = $this->pricelistlines()->where('product_id', '=', $product->id)->first();

        if ( !$line ) $line = $this->addLine( $product );

        return $line;
    }

    public function updateLine( \App\Product $product, $price = null )
    {
        if ($price === null) $price = $this->calculatePrice( $product );

        $line = $this->pricelistlines()->where('product_id', '=', $product->id)->first();

        if ( !$line ) $line = $this->addLine( $product, $price );
        else          $line->update( ['price' => $price] );

        return $line;
    }

    public function removeLine( \App\Product $product )
    {
        $line = $this->pricelistlines()->where('product_id', '=', $product->id)->first();

        $line->delete();
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