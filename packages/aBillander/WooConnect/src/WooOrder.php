<?php

namespace aBillander\WooConnect;

use Illuminate\Database\Eloquent\Model;

class WooOrder extends Model
{
    protected $dates = ['deleted_at', 'date_created', 'date_abi_exported'];
	
//    protected $fillable = [ ];

    protected $guarded = [];

    protected $order_data = [];

    
    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */
    
    public static function import( $woo_order = [] )
    {
        //

        return $woo_order;
    }
    
    
    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    
    // Custom function
    public static function getOrderReference( $order = [] )
    {
		$r = '';

		if ( isset( $order['id'] ) ) {
			$r = 'WooC #'.$order['id'];
		}
		
		return $r;
    }

    public static function getDocumentNotes( $order = [] )
    {
		$n = '';

		if ( isset($order['customer_note']) ) {
			$n .= $order['customer_note']."\n";
		}

		if ( isset( $order['coupon_lines'] ) ) {
	        // Coupons
	        $dp = array();
	        foreach ($order['coupon_lines'] as $coupon) {
	            $dp[] = '"'.$coupon['code'].'"';
	        }
	        $n .= ( count($dp) ? l('Coupons').': '.join(', ', $dp).'.'."\n" : '' );
		}
		
		return $n;
    }

    public static function getVatNumber( $order = [] )
    {
		$vn = '';

		if ( isset( $order['meta_data'] ) ) {
			foreach($order['meta_data']  as $data ) {
				if( $data['key'] == 'CIF/NIF' ) {
					$vn = $data['value'];
					break;
				}
			}
		}
		
		return $vn;
    }

    public static function getBillingAddressId( $order = [] )
    {
		return self::getAddressId( $order, 'billing' );
    }

    public static function getShippingAddressId( $order = [] )
    {
		return self::getAddressId( $order, 'shipping' );
    }

    public static function getAddressId( $order = [], $address = 'billing' )
    {
		$vn = '';

		if ( isset( $order[$address] ) ) {
			$str = $order[$address]['address_1'].
			       $order[$address]['address_2'].
			       $order[$address]['postcode'].
			       $order[$address]['city'];
			$vn = md5($str);
		}
		
		return $vn;
    }

    
    public static function getExportedAt( $meta_data = [], $meta_key = 'date_abi_exported' )
    {
        $date = null;
		$collection = collect($meta_data);

		$meta = 

		$collection->first(function ($item, $key) use ($meta_key) {
		    if ($item['key']==$meta_key) return $item;
		});

		if ($meta) $date = $meta['value'];

		return $date;
    }
    
    public static function getShippingMethodId( $shipping_lines = [] )
    {
        $smi = isset( $shipping_lines[0]['method_id'] ) ? $shipping_lines[0]['method_id'] : '';

        return $smi;
    }
    
    public static function getShippingMethodTitle( $shipping_lines = [] )
    {
        $smt = isset( $shipping_lines[0]['method_title'] ) ? $shipping_lines[0]['method_title'] : '';

        return $smt;
    }
    
    public static function getDate( $date = '' )
    {
        $d = str_replace(['T', 't'], ' ', $date);

        return $d;
    }
    
    public static function getDate_gmt( $date = '' )
    {
        if ( !$date ) return (string) \Carbon\Carbon::now();

        $d = str_replace(['T', 't'], ' ', $date);

        return $d;
    }
    
    public static function getState( $state = '', $country = '' )
    {
        $s = \App\State::findByIsoCode( (strpos($state, '-') ? '' : $country.'-').$state );

        return $s;
    }
    
    public static function getPaymentMethodId( $method = '' )
    {
        if (!$method) return 0;

        // Dictionary
        $gates = json_decode(\App\Configuration::get('WOOC_PAYMENT_GATEWAYS_DICTIONARY_CACHE'), true);

        return isset($gates[$method]) ? $gates[$method] : 0;

        // return \App\Configuration::get('DEF_CUSTOMER_PAYMENT_METHOD');
    }
    
    public static function getTaxId( $slug = '' )
    {
        if (!$slug) return 0;

        // Dictionary
        $taxes = json_decode(\App\Configuration::get('WOOC_TAXES_DICTIONARY_CACHE'), true);

        return isset($taxes[$slug]) ? $taxes[$slug] : 0;
    }
}
