<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'price_list_product';
    
    protected $guarded = [  ];

    public static $rules = array(
    	);


    public function getPriceTaxExcludedAttribute($value)
    {
        if ( ($value->pricelist->type == 0) AND $value->pricelist->price_is_tax_inc ) 
        // ^- ErrorException in Price.php line 22: Trying to get property of non-object
            return $value/(1.0+($value->product->tax->percent/100.0));
        else
            return $value;
    } 
    
    public function price_tax_excl()
    {
        $pList = \App\PriceList::find($this->price_list_id);

        if ( ($pList->type == 0) AND $pList->price_is_tax_inc ) {
            $product = \App\Product::with('tax')->find($this->product_id);
            return $this->price/(1.0+($product->tax->percent/100.0));
            }
        else
            return $this->price;
    }
	
    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function pricelists()
    {
        return $this->hasMany('App\PriceList');
    }
    
    public function products()
    {
        return $this->hasMany('App\Product');
    }
    
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    
    public function pricelist()
    {
        return $this->belongsTo('App\PriceList', 'price_list_id');
    }
	
}