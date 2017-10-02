<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Lang as Lang;

class Sequence extends Model {

    use SoftDeletes;

    public static $types = array(
            'Product', 
            'Customer', 
            'CustomerInvoice',
            'StockCount',
        );

    // Move this to config folder? Maybe yes...
    public static $models = array(
            \App\Product::class         => 'Product', 
            \App\Customer::class        => 'Customer', 
            \App\CustomerInvoice::class => 'CustomerInvoice',
        );

    protected $dates = ['deleted_at', 'last_date_used'];
	
    protected $fillable = [ 'name', 'model_name', 
    						'prefix', 'length', 'separator', 
    						'next_id', 'active'
                          ];

    public static $rules = array(
    	'name'    => 'required|min:2|max:128',
    	'model_name' => 'required',
    	'next_id' => 'integer|min:0',
    	);

    
    public static function listFor( $model = '' )
    {
        if ( !$model ) return [];

        return \App\Sequence::where('model_name', '=', $model)->pluck('name', 'id')->toArray();
    }

    public static function documentList()
    {
        $list = array();

        $types = (self::$types);

        foreach($types as $type)
            $list[$type]    = Lang::get('appmultilang.'.$type);

        return $list;
    }

    public function getFormatAttribute()
    {
        $format = $this->prefix . $this->separator . str_pad('XX', $this->length, '0', STR_PAD_LEFT);

        return $format;
    }

	public function getNextDocumentId() {
		$docId = $this->next_id;
		$this->next_id++;
		$this->last_date_used = \Carbon\Carbon::now();
		$this->save();

		return $docId;
	}

    public function getDocumentReference($id = 0)
    {
        $format = $this->prefix . $this->separator . str_pad(strval(intval($id)), $this->length, '0', STR_PAD_LEFT);

        return $format;
    }
}
