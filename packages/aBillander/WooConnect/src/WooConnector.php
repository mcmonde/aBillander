<?php 

namespace aBillander\WooConnect;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use WooCommerce;
use Automattic\WooCommerce\HttpClient\HttpClientException as WooHttpClientException;

class WooConnector {    // extends Model {

    public static $statuses = array(
            'pending', 
            'processing', 
            'on-hold',
            'completed',
            'cancelled',
            'refunded',
            'failed',
        );

//    protected $dates = ['deleted_at'];



    public static function getOrderStatusList()
    {
            $list = [];
            foreach (self::$statuses as $status) {
                $list[$status] = l($status, [], 'woocommerce');
            }

            return $list;
    }

    public static function getTaxKey( $slug = '' )
    {
            return 'WOOC_TAX_'.strtoupper($slug);
    }

    public static function getPaymentGatewayKey( $id = '' )
    {
            return 'WOOC_PAYMENT_GATEWAY_'.strtoupper($id);
    }


/* ********************************************************************************************* */   


    public static function getWooConfigurations()
    {
        // 

        return [];
    }
    
    public static function getWooSetting( $setting_id = '' )
    {
        $settings = json_decode(\App\Configuration::get('WOOC_CONFIGURATIONS_CACHE'), true);

        // abi_r($settings);

        if ( !$setting_id || !$settings ) return null;

        $filtered = array_filter($settings, function($v) use ($setting_id) {
            return $v['id'] == $setting_id;
        });

        // abi_r($filtered);

        // Return: string or array
        return $filtered[0]['value'];
    }

    
    public static function getWooOrder( $order_id = 0 )
    {
        $oID = intval($order_id);

        if ( !($oID>0) ) {
            return [];
        }

        // Do the Mambo!!!
        $params = [
//          'dp'   => 6,        // WooCommerce serve store some values rounded. Not useful this option. Use WooCommerce API default instead: 2 decimal places
            'dp'   => \App\Configuration::get('WOOC_DECIMAL_PLACES'),
        ];

        // Get Order fromm WooCommerce Shop
        try {

            $order = WooCommerce::get('orders/'.$oID, $params); // Array
        }

        catch( WooHttpClientException $e ) {

            /*
            $e->getMessage(); // Error message.

            $e->getRequest(); // Last request data.

            $e->getResponse(); // Last response data.
            */

            $order = [];
            // So far, we do not know if order_id does not exist, or connection fails. 
            // does it matter? -> Anyway, no order is issued

        }

//      abi_r($order, true);

        return $order;
    }

    
    public static function getWooTaxes()
    {
        // Do the Mambo!!!
        try {

            $taxes = WooCommerce::get('taxes/classes'); // Array
        }

        catch( WooHttpClientException $e ) {

            /*
            $e->getMessage(); // Error message.

            $e->getRequest(); // Last request data.

            $e->getResponse(); // Last response data.
            */

            $taxes = [];

        }

        return $taxes;
    }

    
    public static function getWooPaymentGateways()
    {
        // Do the Mambo!!!
        try {

            $gateways = WooCommerce::get('payment_gateways');   // Array
        }

        catch( WooHttpClientException $e ) {

            /*
            $e->getMessage(); // Error message.

            $e->getRequest(); // Last request data.

            $e->getResponse(); // Last response data.
            */

            $gateways = [];

        }

//      abi_r($gateways, true);

        return $gateways;
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