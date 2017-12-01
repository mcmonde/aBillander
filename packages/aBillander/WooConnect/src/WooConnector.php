<?php 

namespace aBillander\WooConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WooConnector extends Model {

    public static $statuses = array(
            'pending', 
            'processing', 
            'on-hold',
            'completed',
            'cancelled',
            'refunded',
            'failed',
        );

    protected $dates = ['deleted_at'];



    public static function getOrderStatusList()
    {
            $list = [];
            foreach (self::$statuses as $status) {
                $list[$status] = l($status, [], 'woocommerce');
            }

            return $list;
    }

    /**
     * Alias function
     */
    
    public static function convertAmount($amount, Currency $currency_from = null, Currency $currency_to = null)
    {
        // 
    }
    

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function customerinvoices()
    {
        // 
    }
}