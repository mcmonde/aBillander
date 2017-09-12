<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ViewFormatterTrait;

class Product extends Model {

    use ViewFormatterTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [ 'name', 'reference', 'ean13', 'description', 
                            'quantity_onhand', 'warranty_period', 
                            'reorder_point', 'maximum_stock', 'price', 'cost_price', 'supply_lead_time', 
                            'location', 'width', 'height', 'depth', 'weight', 
                            'notes', 'stock_control', 'publish_to_web', 'blocked', 'active', 
                            'tax_id', 'category_id', 'main_supplier_id',
                          ];

    public static $rules = array(
        'create' => array(
                        	'name'        => 'required|min:2|max:128',
                        	'reference'   => 'required|min:2|max:32|unique:products,reference', 
                            'price'       => 'required|numeric|min:0',
                            'cost_price'      => 'required|numeric|min:0',
                            'tax_id'      => 'exists:taxes,id',
                            'category_id' => 'exists:categories,id',
                            'quantity_onhand' => 'required|numeric|min:0',
                            'warehouse_id' => 'exists:warehouses,id',
                    ),
        'main_data' => array(
                            'name'        => 'required|min:2|max:128',
                            'reference'   => 'sometimes|required|min:2|max:32|unique:products,id,',     // https://laracasts.com/discuss/channels/requests/laravel-5-validation-request-how-to-handle-validation-on-update
                            'tax_id'      => 'exists:taxes,id',
                            'category_id' => 'exists:categories,id',
                    ),
        'purchases' => array(
                            
                    ),
        'sales' => array(
                            
                    ),
        'inventory' => array(
                            
                    ),
        'internet' => array(
                            
                    ),
        );


    public function scopeFilter($query, $params)
    {
        if ( isset($params['reference']) && trim($params['reference']) !== '' )
        {
            $query->where('reference', 'LIKE', '%' . trim($params['reference']) . '%');
            // $query->orWhere('combinations.reference', 'LIKE', '%' . trim($params['reference'] . '%'));
        }

        if ( isset($params['name']) && trim($params['name']) !== '' )
        {
            $query->where('name', 'LIKE', '%' . trim($params['name'] . '%'));
        }

        if ( isset($params['stock']) )
        {
            if ( $params['stock'] == 0 )
                $query->where('quantity_onhand', '<=', 0);
            if ( $params['stock'] == 1 )
                $query->where('quantity_onhand', '>', 0);
        }

        if ( isset($params['category_id']) && $params['category_id'] > 0 )
        {
            $query->where('category_id', '=', $params['category_id']);
        }

        if ( isset($params['active']) )
        {
            if ( $params['active'] == 0 )
                $query->where('active', '=', 0);
            if ( $params['active'] == 1 )
                $query->where('active', '>', 0);
        }

        return $query;
    }
    

    public function getStockByWarehouse( $warehouse )
    { 
        $wh_id = is_numeric($warehouse)
                    ? $warehouse
                    : $warehouse->id ;

    //    $product = \App\Product::find($this->id);

        $whs = $this->warehouses;
        if ($whs->contains($wh_id)) {
            $wh = $this->warehouses()->get();
            $wh = $wh->find($wh_id);
            $quantity = $wh->pivot->quantity;
        } else {
            $quantity = 0;
        }

        return $quantity;
    }
    

    public function getFeaturedImage()
    { 
        // If no featured image, return one, anyway
        return $this->images()->orderBy('is_featured', 'desc')->orderBy('position', 'asc')->first();
    }

    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function images()
    {
        return $this->morphMany('App\Image', 'imageable');
    }

    public function tax()
    {
        return $this->belongsTo('App\Tax');
	}
		
    public function category()
    {
        return $this->belongsTo('App\Category');
	}
    
    public function combinations()
    {
        return $this->hasMany('App\Combination');
    }
    
    public function stockmovements()
    {
        return $this->hasMany('App\StockMovement');
    }
    
    public function warehouses()
    {
        return $this->belongsToMany('App\Warehouse')->withPivot('quantity')->withTimestamps();
    }
    
    public function pricelists()
    {
        return $this->belongsToMany('App\PriceList', 'price_list_product', 'product_id', 'price_list_id')->withPivot('price')->withTimestamps();
    }
    
/*    public function pricelist( $list_id = null )
    {
        if ( $list_id > 0 )
            return $this->belongsToMany('App\PriceList')->where('price_list_id', '=', $list_id)->withPivot('price')->withTimestamps();
    } */
    
    public function prices()
    {
        return $this->hasMany('App\Price');
    }
    
    public function price_list( $list )
    {
        $plist_id = is_numeric($list)
                    ? $list
                    : $list->id ;

        $result = $this->hasMany('App\Price')->where('price_list_id', '=', $plist_id)->first();

        if ( $result )
            return $result;
       
        // Price not foiund, calculate it
        $product = \App\Product::find($this->id);

        $priceObj = \App\Price::create([]);
        $priceObj->product_id = $product->id;
        $priceObj->price_list_id = $plist_id;

        $price = \App\PriceList::priceCalculator( \App\PriceList::find($plist_id), $product );
        $priceObj->price = $price;
        
        $product->pricelists()->attach($plist_id, ['price' => $price]);

            return $priceObj;
    }
    

    /*
    |--------------------------------------------------------------------------
    | Data Provider
    |--------------------------------------------------------------------------
    */

    /**
     * Provides a json encoded array of matching product names
     * @param  string $query
     * @return json
     */
    public static function searchByNameAutocomplete($query, $onhand_only = 0)
    {
        $q = Product::select('*', 'products.id as product_id', 'taxes.id as tax_id', 
                                  'products.name as product_name', 'taxes.name as tax_name')
                    ->leftjoin('taxes','taxes.id','=','products.tax_id')
                    ->orderBy('products.name')
                    ->where('products.name', 'like', '%' . $query . '%');

        if ($onhand_only) $q = $q->where('products.quantity_onhand', '>', '0');

         $products = $q->get();

         return json_encode( array('query' => $query, 'suggestions' => $products) );
    }
    

    /*
    |--------------------------------------------------------------------------
    | Price calculations
    |--------------------------------------------------------------------------
    */

    /**
     * Return a json list of records matching the provided query
     *
     * @return json
     */
    public function price( \App\Customer $customer )
    {
        // First: Customer has pricelist?
        if ($customer->pricelist) {

            // return \App\PriceList::priceCalculator( $customer->pricelist, $this );
            return $this->price_list( $customer->pricelist )->price;
        } 

        // Second: Customer Group has pricelist?
        if ($customer->customergroup AND $customer->customergroup->pricelist) {

            // return \App\PriceList::priceCalculator( $customer->customergroup->pricelist, $this );
            return $this->price_list( $customer->customergroup->pricelist )->price;
        }

        // Otherwise, use product price (initial or base price)
        return $this->price;
    }
	
}