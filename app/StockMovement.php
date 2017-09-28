<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ViewFormatterTrait;

use \Lang as Lang;

class StockMovement extends Model {

    use ViewFormatterTrait;
    use SoftDeletes;

    protected $dates = ['date', 'deleted_at'];

    protected $fillable = ['date', 'document_reference', 'price', 'currency_id', 'conversion_rate', 'quantity', 'notes',
                           'product_id', 'combination_id', 'warehouse_id', 'movement_type_id'];

    public static $rules = array(
        'date' => 'required',
//        'date' => 'required|date|date_format:YY-MM-DD',
//         See: https://es.stackoverflow.com/questions/57020/validaci%C3%B3n-de-formato-de-fecha-no-funciona-laravel-5-3
//        'document_reference' => 'required',   <- Stock adjustments & others do not need it!!
        'price' => 'required',
        'currency_id' => 'exists:currencies,id',
        'quantity' => 'required|not_in:0',
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
    const TRANSFER_OUT         = 40;
    const TRANSFER_IN          = 41;
	const MANUFACTURING_INPUT  = 50;
	const MANUFACTURING_RETURN = 51;
	const MANUFACTURING_OUTPUT = 55;
    
	public static function stockmovementList()
    {
        $list = array();

        $list[self::INITIAL_STOCK]        = self::INITIAL_STOCK.       ' - '.Lang::get('appmultilang.'.StockMovement::INITIAL_STOCK);
        $list[self::ADJUSTMENT]           = self::ADJUSTMENT.          ' - '.Lang::get('appmultilang.'.StockMovement::ADJUSTMENT);
        $list[self::PURCHASE_ORDER]       = self::PURCHASE_ORDER.      ' - '.Lang::get('appmultilang.'.StockMovement::PURCHASE_ORDER);
        $list[self::PURCHASE_RETURN]      = self::PURCHASE_RETURN.     ' - '.Lang::get('appmultilang.'.StockMovement::PURCHASE_RETURN);
        $list[self::SALE_ORDER]           = self::SALE_ORDER.          ' - '.Lang::get('appmultilang.'.StockMovement::SALE_ORDER);
        $list[self::SALE_RETURN]          = self::SALE_RETURN.         ' - '.Lang::get('appmultilang.'.StockMovement::SALE_RETURN);
        $list[self::TRANSFER_OUT]         = self::TRANSFER_OUT.        ' - '.Lang::get('appmultilang.'.StockMovement::TRANSFER_OUT);
        $list[self::TRANSFER_IN]          = self::TRANSFER_IN.         ' - '.Lang::get('appmultilang.'.StockMovement::TRANSFER_IN);
        $list[self::MANUFACTURING_INPUT]  = self::MANUFACTURING_INPUT. ' - '.Lang::get('appmultilang.'.StockMovement::MANUFACTURING_INPUT);
        $list[self::MANUFACTURING_RETURN] = self::MANUFACTURING_RETURN.' - '.Lang::get('appmultilang.'.StockMovement::MANUFACTURING_RETURN);
        $list[self::MANUFACTURING_OUTPUT] = self::MANUFACTURING_OUTPUT.' - '.Lang::get('appmultilang.'.StockMovement::MANUFACTURING_OUTPUT);

//        echo '<pre>';print_r(array('0' => '-- Seleccione--') + $list);echo '</pre>';


        return $list;
	}
    
    
    /*
    |--------------------------------------------------------------------------
    | Stock movement fulfillment (perform stock movements)
    |--------------------------------------------------------------------------
    */

    public function process()
    {
        $list = $this->stockmovementList();
        if ( isset($list[$this->movement_type_id]) ) 
            return $this->{'process_'.$this->movement_type_id}();
        else
            return false;
    }

    // INITIAL_STOCK
    public function process_10()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        if ( $product->quantity_onhand > 0.0 ) return 'Not allowed';
        
        $quantity_onhand = $this->quantity;

        // Average price stuff
        if ( !($this->combination_id > 0) ) {
            $product->cost_average = $this->price;
            $product->last_purchase_price = 0.0;
        }

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $this->quantity;

            $combination->cost_average = $this->price;
            $combination->last_purchase_price = 0.0;

            $combination->quantity_onhand = $quantity_onhand;
            $combination->save();
        }

        // Update Product-Warehouse relationship (quantity)
        $whs = $product->warehouses;
        if ($whs->contains($this->warehouse_id)) {
            $wh = $product->warehouses()->get();
            $wh = $wh->find($this->warehouse_id);
            $quantity = $this->quantity;
            
            if ($quantity != 0) {
                $wh->pivot->quantity = $quantity;
                $wh->pivot->save(); }
            else {
                // Delete record ($quantity = 0)
                $product->warehouses()->detach($this->warehouse_id); }
        } else {
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
        if ($this->combination_id > 0) {
            $whs = $combination->warehouses;
            if ($whs->contains($this->warehouse_id)) {
                $wh = $combination->warehouses()->get();
                $wh = $wh->find($this->warehouse_id);
                $quantity = $this->quantity;
                
                if ($quantity != 0) {
                    $wh->pivot->quantity = $quantity;
                    $wh->pivot->save(); }
                else {
                    // Delete record ($quantity = 0)
                    $combination->warehouses()->detach($this->warehouse_id); }
            } else {
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    // ADJUSTMENT
    public function process_12()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $this->quantity;

        // Average price stuff - Not needed!

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $this->quantity;

            // Average price stuff - Not needed!

            $combination->quantity_onhand = $quantity_onhand;
            $combination->save();
        }

        // Update Product-Warehouse relationship (quantity)
        $whs = $product->warehouses;
        if ($whs->contains($this->warehouse_id)) {
            $wh = $product->warehouses()->get();
            $wh = $wh->find($this->warehouse_id);
            $quantity = $this->quantity;
            
            if ($quantity != 0) {
                $wh->pivot->quantity = $quantity;
                $wh->pivot->save(); }
            else {
                // Delete record ($quantity = 0)
                $product->warehouses()->detach($this->warehouse_id); }
        } else {
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
        if ($this->combination_id > 0) {
            $whs = $combination->warehouses;
            if ($whs->contains($this->warehouse_id)) {
                $wh = $combination->warehouses()->get();
                $wh = $wh->find($this->warehouse_id);
                $quantity = $this->quantity;
                
                if ($quantity != 0) {
                    $wh->pivot->quantity = $quantity;
                    $wh->pivot->save(); }
                else {
                    // Delete record ($quantity = 0)
                    $combination->warehouses()->detach($this->warehouse_id); }
            } else {
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    // PURCHASE_ORDER
    public function process_20()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $product->quantity_onhand + $this->quantity;

        // Average price stuff
        if ( !($this->combination_id > 0) ) {
            // $cost = $product->cost_average;
            $cost_average = ($product->quantity_onhand * $product->cost_average + $this->quantity * $this->price) / ($product->quantity_onhand + $this->quantity);

            $product->cost_average = $cost_average;
            $product->last_purchase_price = $this->price;
        }

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $combination->quantity_onhand + $this->quantity;

            // Average price stuff
            // $cost = $combination->cost_average;
            $cost_average = ($combination->quantity_onhand * $combination->cost_average + $this->quantity * $this->price) / ($combination->quantity_onhand + $this->quantity);
            
            $combination->cost_average = $cost_average;
            $combination->last_purchase_price = $this->price;

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
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
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
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    // PURCHASE_RETURN
    public function process_21()
    {
        return true;
    }

    // SALE_ORDER
    public function process_30()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $product->quantity_onhand - $this->quantity;

        // Average price stuff - Not needed!

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $combination->quantity_onhand - $this->quantity;

            // Average price stuff - Not needed!

            $combination->quantity_onhand = $quantity_onhand;
            $combination->save();
        }

        // Update Product-Warehouse relationship (quantity)
        $whs = $product->warehouses;
        if ($whs->contains($this->warehouse_id)) {
            $wh = $product->warehouses()->get();
            $wh = $wh->find($this->warehouse_id);
            $quantity = $wh->pivot->quantity - $this->quantity;
            
            if ($quantity != 0) {
                $wh->pivot->quantity = $quantity;
                $wh->pivot->save(); }
            else {
                // Delete record ($quantity = 0)
                $product->warehouses()->detach($this->warehouse_id); }
        } else {
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
        if ($this->combination_id > 0) {
            $whs = $combination->warehouses;
            if ($whs->contains($this->warehouse_id)) {
                $wh = $combination->warehouses()->get();
                $wh = $wh->find($this->warehouse_id);
                $quantity = $wh->pivot->quantity - $this->quantity;
                
                if ($quantity != 0) {
                    $wh->pivot->quantity = $quantity;
                    $wh->pivot->save(); }
                else {
                    // Delete record ($quantity = 0)
                    $combination->warehouses()->detach($this->warehouse_id); }
            } else {
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    // SALE_RETURN
    public function process_31()
    {
        return true;
    }

    // TRANSFER_OUT
    public function process_40()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $product->quantity_onhand - $this->quantity;

        // Average price stuff - Not needed!

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $combination->quantity_onhand - $this->quantity;

            // Average price stuff - Not needed!

            $combination->quantity_onhand = $quantity_onhand;
            $combination->save();
        }

        // Update Product-Warehouse relationship (quantity)
        $whs = $product->warehouses;
        if ($whs->contains($this->warehouse_id)) {
            $wh = $product->warehouses()->get();
            $wh = $wh->find($this->warehouse_id);
            $quantity = $wh->pivot->quantity - $this->quantity;
            
            if ($quantity != 0) {
                $wh->pivot->quantity = $quantity;
                $wh->pivot->save(); }
            else {
                // Delete record ($quantity = 0)
                $product->warehouses()->detach($this->warehouse_id); }
        } else {
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
        if ($this->combination_id > 0) {
            $whs = $combination->warehouses;
            if ($whs->contains($this->warehouse_id)) {
                $wh = $combination->warehouses()->get();
                $wh = $wh->find($this->warehouse_id);
                $quantity = $wh->pivot->quantity - $this->quantity;
                
                if ($quantity != 0) {
                    $wh->pivot->quantity = $quantity;
                    $wh->pivot->save(); }
                else {
                    // Delete record ($quantity = 0)
                    $combination->warehouses()->detach($this->warehouse_id); }
            } else {
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    // TRANSFER_IN
    public function process_41()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $product->quantity_onhand + $this->quantity;

        // Average price stuff - Not needed!

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $combination->quantity_onhand + $this->quantity;

            // Average price stuff - Not needed!

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
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
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
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    // MANUFACTURING_INPUT
    public function process_50()
    {
        return true;
    }

    // MANUFACTURING_RETURN
    public function process_51()
    {
        return true;
    }

    // MANUFACTURING_OUTPUT
    public function process_55()
    {
        return true;
    }
    
    public function fulfill()
    {
        // Update Product
        $product = \App\Product::find($this->product_id);
        $quantity_onhand = $product->quantity_onhand + $this->quantity;

        // Average price stuff
        if ( !($this->combination_id > 0) ) {
            // $cost = $product->cost_average;
            $cost_average = ($product->quantity_onhand * $product->cost_average + $this->quantity * $this->price) / ($product->quantity_onhand + $this->quantity);

            $product->cost_average = $cost_average;
            $product->last_purchase_price = $this->price;
        }

        $product->quantity_onhand = $quantity_onhand;
        $product->save();

        // Update Cpmbination
        if ($this->combination_id > 0) {
            $combination = \App\Combination::find($this->combination_id);
            $quantity_onhand = $combination->quantity_onhand + $this->quantity;

            // Average price stuff
            // $cost = $combination->cost_average;
            $cost_average = ($combination->quantity_onhand * $combination->cost_average + $this->quantity * $this->price) / ($combination->quantity_onhand + $this->quantity);
            
            $combination->cost_average = $cost_average;
            $combination->last_purchase_price = $this->price;

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
            if ($this->quantity != 0) 
                $product->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
        }

        // Update Combination-Warehouse relationship (quantity)
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
                if ($this->quantity != 0) 
                    $combination->warehouses()->attach($this->warehouse_id, array('quantity' => $this->quantity));
            }
        }
    }

    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all of the owning stockmovementable models.
     */
    public function stockmovementable()
    {
        return $this->morphTo();
    }
    

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