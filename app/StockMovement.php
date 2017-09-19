<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Lang as Lang;

class StockMovement extends Model {

    use SoftDeletes;

    protected $dates = ['date', 'deleted_at'];

    protected $fillable = ['date', 'document_reference', 'price', 'quantity', 'notes',
                           'product_id', 'combination_id', 'warehouse_id', 'movement_type_id'];

    public static $rules = array(
        'date' => 'date',
        'price' => 'required',
        'quantity' => 'required',
        'product_id' => 'exists:products,id',
        'combination_id' => 'sometimes|exists:combinations,id',
        'warehouse_id' => 'exists:warehouses,id',
        'movement_type_id' => 'required',
    );

    public static $rules_adjustment = array(
        'date' => 'date',
//        'price' => 'required',
        'quantity' => 'required|min:0',
        'product_id' => 'exists:products,id',
        'combination_id' => 'sometimes|exists:combinations,id',
        'warehouse_id' => 'exists:warehouses,id',
//        'movement_type_id' => 'required',
    );
	
    
    /*
    |--------------------------------------------------------------------------
    | Stock movement types
    |--------------------------------------------------------------------------
    */

    const INITIAL_STOCK        = 10;
    const ADJUSTMENT           = 12;
	const PURCHASE_ORDER       = 20;
	const PURCHASE_RETURN      = 21;
	const SALE_ORDER           = 30;
	const SALE_RETURN          = 31;
	const TRANSFER             = 40;
	const MANUFACTURING_IN     = 50;
	const MANUFACTURING_RETURN = 51;
	const MANUFACTURING_OUT    = 55;
    
	public static function stockmovementList()
    {
        $list = array();

        $list[self::INITIAL_STOCK]        = self::INITIAL_STOCK.' - '.Lang::get('appmultilang.'.StockMovement::INITIAL_STOCK);
        $list[self::ADJUSTMENT]           = self::ADJUSTMENT.' - '.Lang::get('appmultilang.'.StockMovement::ADJUSTMENT);
        $list[self::PURCHASE_ORDER]       = self::PURCHASE_ORDER.' - '.Lang::get('appmultilang.'.StockMovement::PURCHASE_ORDER);
        $list[self::PURCHASE_RETURN]      = self::PURCHASE_RETURN.' - '.Lang::get('appmultilang.'.StockMovement::PURCHASE_RETURN);
        $list[self::SALE_ORDER]           = self::SALE_ORDER.' - '.Lang::get('appmultilang.'.StockMovement::SALE_ORDER);
        $list[self::SALE_RETURN]          = self::SALE_RETURN.' - '.Lang::get('appmultilang.'.StockMovement::SALE_RETURN);
        $list[self::TRANSFER]             = self::TRANSFER.' - '.Lang::get('appmultilang.'.StockMovement::TRANSFER);
        $list[self::MANUFACTURING_IN]     = self::MANUFACTURING_IN.' - '.Lang::get('appmultilang.'.StockMovement::MANUFACTURING_IN);
        $list[self::MANUFACTURING_RETURN] = self::MANUFACTURING_RETURN.' - '.Lang::get('appmultilang.'.StockMovement::MANUFACTURING_RETURN);
        $list[self::MANUFACTURING_OUT]    = self::MANUFACTURING_OUT.' - '.Lang::get('appmultilang.'.StockMovement::MANUFACTURING_OUT);

//        echo '<pre>';print_r(array('0' => '-- Seleccione--') + $list);echo '</pre>';


        return $list;
	}
    
    
    /*
    |--------------------------------------------------------------------------
    | Stock movement fulfillment (perform stock movements)
    |--------------------------------------------------------------------------
    */
    
    public function fulfill()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $product->quantity_onhand + $this->quantity;

        // Average price stuff... (ToDo)
        
        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $combination->quantity_onhand + $this->quantity;

            $combination->quantity_onhand = $quantity_onhand;
            $combination->save();
        }

        // Update Product-Warehouse relationship (quantity)
        $whs = $product->warehouses;
        if ($whs->contains($this->warehouse_id)) {
            $wh = $product->warehouses()->get();
            $wh = $wh->find($this->warehouse_id);
            $quantity = $wh->pivot->quantity + $this->quantity;
            
            if ($quantity != 0) {
                $wh->pivot->quantity = $quantity;
                $wh->pivot->save(); }
            else {
                // Delete record ($quantity = 0)
                $product->warehouses()->detach($this->warehouse_id); }
        } else {
            $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        if ($this->combination_id > 0) {
            $whs = $combination->warehouses;
            if ($whs->contains($this->warehouse_id)) {
                $wh = $combination->warehouses()->get();
                $wh = $wh->find($this->warehouse_id);
                $quantity = $wh->pivot->quantity + $this->quantity;
                
                if ($quantity != 0) {
                    $wh->pivot->quantity = $quantity;
                    $wh->pivot->save(); }
                else {
                    // Delete record ($quantity = 0)
                    $combination->warehouses()->detach($this->warehouse_id); }
            } else {
                $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    
    public function combination()
    {
        return $this->belongsTo('App\Combination');
    }
	
    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
	}
    
	public function movementtype()
    {
        return $this->belongsTo('MovementType');
	}
    
	public function user()
    {
        return $this->belongsTo('App\User');
	}
}