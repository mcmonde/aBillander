<?php 

namespace aBillander\WooConnect;

use WooCommerce;
use Automattic\WooCommerce\HttpClient\HttpClientException as WooHttpClientException;

use App\Customer as Customer;
use App\Address as Address;

// use \aBillander\WooConnect\WooOrder;

class WooOrderImporter {

	protected $wc_order;
	protected $run_status = true;		// So far, so good. Can continue export

	protected $raw_data = array();

	protected $currency;				// aBillander Object
	protected $customer;				// aBillander Object
	protected $invoicing_address_id;
	protected $shipping_address_id;

	protected $info     = array();
	protected $billing  = array();
	protected $delivery = array();
	protected $products = array();
	protected $shipping = array();
	protected $fees     = array();
	protected $coupons  = array();
	protected $taxes    = array();

	// Logger to send messages
	protected $log;

    public function __construct ($order_id = null)
    {
        // Get logger
        // $this->log = $rwsConnectorLog;

        $this->run_status = true;

        // Get order data (if order exists!)
        if ( intval($order_id) ) {
            // set the product
            // $this->fill_in_data( intval($order_id) );

            // fill it, parse it and save it
            // $this->populate_data();
            
            // $this->import();  // The whole process

			$this->fill_in_data( intval($order_id) );
            // return true

        } else
        	;
    }
    
    /**
     *   Data retriever & Transformer
     */
    public function fill_in_data($order_id = null)
    {
        // 
    	// Get $order_id data...
        $data = $this->raw_data = self::getWooOrder( intval($order_id) );
        if (!$data) {
            $this->logMessage( 'ERROR', 'Se ha intentado recuperar el Pedido número <b>"'.$order_id.'"</b> y no existe.' );
            $this->run_status = false;
        }

        return $this->run_status;
    }

    public static function makeInvoice( $order_id = null ) {
        // See: https://stackoverflow.com/questions/1699796/best-way-to-do-multiple-constructors-in-php
        $importer = new static($order_id);
        if (!$importer->tell_run_status()) return 0;

        $importer->setCurrency();

        $importer->setCustomer();

//        return $invoice_id;
    }
        
    public function setCurrency()
    {
        $order = $this->raw_data;

        $currency = \App\Currency::findByIsoCode( $order['currency'] );
        if (!$currency) {
        	$currency = \App\Currency::findByIsoCode( \App\Configuration::get('WOOC_DEF_CURRENCY') );
        }
        // To Do: throw error if currency is not found
        $this->currency = $currency;
    }

    public function setCustomer()
    {
        // Check if Customer exists; Othrewise, import it
        $customer_webshop_id = $this->raw_data['customer_id'];

        $this->customer = Customer::where('webshop_id', $customer_webshop_id )->first();

        if ($this->customer) $this->checkAddresses();

        else                 $this->importCustomer();

        abi_r($this->invoicing_address_id);
        abi_r($this->shipping_address_id);
    }


    /**
     *   Alias function
     */
    public static function dummy()
    {
        // 
    }
    

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */
    
    public function importCustomer()
    {
        // Build Customer data
        $order = $this->raw_data;

        $name = $order['billing']['company'] ? $order['billing']['company'] : 
        		$order['billing']['first_name'].' '.$order['billing']['last_name'];

        $language_id = \App\Configuration::get('WOOC_DEF_LANGUAGE') ? \App\Configuration::get('WOOC_DEF_LANGUAGE') : 
							 \App\Configuration::get('DEF_LANGUAGE');

		$data = [
        	'name_fiscal'     => $name,
			'name_commercial' => $name,

//			'website' => $order[''],

			'identification' => WooOrder::getVatNumber( $order ),

			'webshop_id' => $order['customer_id'],

//			'payment_days' => $order[''],
//			'no_payment_month' => $order[''],

			'outstanding_amount_allowed' => \App\Configuration::get('DEF_OUTSTANDING_AMOUNT'),
//			'outstanding_amount' => $order[''],

			'notes' => $order['customer_note'],
//			'sales_equalization' => $order[''],
//			'allow_login' => $order[''],

			'accept_einvoice' => 1,
			'blocked' => 0,
			'active'  => 1,

			'currency_id' => $this->currency->id,
			'language_id' => $language_id,
		];

		$customer = Customer::create($data);
        $this->customer = $customer;

		// Build Billing address
		$country    = $order['billing']['country'];
		$country_id = null;
		$state      = $order['billing']['state'];
		$state_id   = null;

		$bcountry = \App\Country::findByIsoCode( $order['billing']['country'] );
		if ($bcountry) {
			$country    = $bcountry->name;
			$country_id = $bcountry->id;
		}

		$bstate = WooOrder::getState( $order['billing']['state'], $order['billing']['country'] );
		if ($bstate) {
			$state    = $bstate->name;
			$state_id = $bstate->id;
		}

		$data = [
			'alias' => $order['id'].'-Billing',
			'webshop_id' => WooOrder::getBillingAddressId( $order ),

			'name_commercial' => $name,
			
			'address1' => $order['billing']['address_1'],
			'address2' => $order['billing']['address_2'],
			'postcode' => $order['billing']['postcode'],
			'city'         => $order['billing']['city'],
			'state_name'   => $state,
			'country_name' => $country,
			
			'firstname' => $order['billing']['first_name'],
			'lastname'  => $order['billing']['last_name'],
			'email'     => $order['billing']['email'],

			'phone' => $order['billing']['phone'],
//			'phone_mobile' => $order[''],
//			'fax' => $order[''],
			
			'notes' => null,
			'active' => 1,

//			'latitude' => $order[''],
//			'longitude' => $order[''],

			'state_id'   => $state_id,
			'country_id' => $country_id,
		];

        $address = Address::create($data);
        $customer->addresses()->save($address);

        $customer->invoicing_address_id = $address->id;
        $this->invoicing_address_id     = $address->id;


		// Build Shipping address
if ( WooOrder::getShippingAddressId( $order ) != $data['webshop_id'] ) {
		// Shipping is a new Address. Let's get it!

        $name = $order['shipping']['company'] ? $order['shipping']['company'] : 
        		$order['shipping']['first_name'].' '.$order['shipping']['last_name'];

		$country    = $order['shipping']['country'];
		$country_id = null;
		$state      = $order['shipping']['state'];
		$state_id   = null;

		$scountry = \App\Country::findByIsoCode( $order['shipping']['country'] );
		if ($scountry) {
			$country    = $scountry->name;
			$country_id = $scountry->id;
		}

		$sstate = WooOrder::getState( $order['shipping']['state'], $order['shipping']['country'] );
		if ($sstate) {
			$state    = $sstate->name;
			$state_id = $sstate->id;
		}

		$data = [
			'alias' => $order['id'].'-Shipping',
			'webshop_id' => WooOrder::getShippingAddressId( $order ),

			'name_commercial' => $name,
			
			'address1' => $order['shipping']['address_1'],
			'address2' => $order['shipping']['address_2'],
			'postcode' => $order['shipping']['postcode'],
			'city'         => $order['shipping']['city'],
			'state_name'   => $state,
			'country_name' => $country,
			
			'firstname' => $order['shipping']['first_name'],
			'lastname'  => $order['shipping']['last_name'],
//			'email'     => $order['shipping']['email'],

//			'phone' => $order['shipping']['phone'],
//			'phone_mobile' => $order[''],
//			'fax' => $order[''],
			
			'notes' => null,
			'active' => 1,

//			'latitude' => $order[''],
//			'longitude' => $order[''],

			'state_id'   => $state_id,
			'country_id' => $country_id,
		];

        $address = Address::create($data);
        $customer->addresses()->save($address);

}

        $customer->shipping_address_id = $address->id;
        $this->shipping_address_id = $address->id;

        $customer->save();
    }


    public function checkAddresses()
    {
        // Build Customer data
        $order = $this->raw_data;

        // Build Billing address
		$needle = WooOrder::getBillingAddressId( $order );
		$addr = $this->customer->addresses()->where('webshop_id', $needle )->first();
        if ( $addr ) {

	        $this->invoicing_address_id = $addr->id;

        } else {
        	
        	// Create Address
        	$address = $this->createInvoicingAddress();

        	$this->invoicing_address_id = $address->id;

        }

        // Need to update Customer Invoicing Address?
        if ($this->customer->invoicing_address_id != $this->invoicing_address_id) {
        	$this->customer->update( [ 'invoicing_address_id' => $this->invoicing_address_id ] );
        }


		// Build Shipping address
		$needle = WooOrder::getShippingAddressId( $order );
		$addr = $this->customer->addresses()->where('webshop_id', $needle )->first();
        if ( $addr ) {

        	$this->shipping_address_id = $addr->id;

        } else {
        	
        	// Create Address
        	$address = $this->createShippingAddress();

        	$this->shipping_address_id = $address->id;

        }
    }
    
    public function createInvoicingAddress()
    {
        // Build Customer data
        $order = $this->raw_data;
        $customer = $this->customer;

		// Build Billing address
        $name = $order['billing']['company'] ? $order['billing']['company'] : 
        		$order['billing']['first_name'].' '.$order['billing']['last_name'];

		$country    = $order['billing']['country'];
		$country_id = null;
		$state      = $order['billing']['state'];
		$state_id   = null;

		$bcountry = \App\Country::findByIsoCode( $order['billing']['country'] );
		if ($bcountry) {
			$country    = $bcountry->name;
			$country_id = $bcountry->id;
		}

		$bstate = WooOrder::getState( $order['billing']['state'], $order['billing']['country'] );
		if ($bstate) {
			$state    = $bstate->name;
			$state_id = $bstate->id;
		}

		$data = [
			'alias' => $order['id'].'-Billing',
			'webshop_id' => WooOrder::getBillingAddressId( $order ),

			'name_commercial' => $name,
			
			'address1' => $order['billing']['address_1'],
			'address2' => $order['billing']['address_2'],
			'postcode' => $order['billing']['postcode'],
			'city'         => $order['billing']['city'],
			'state_name'   => $state,
			'country_name' => $country,
			
			'firstname' => $order['billing']['first_name'],
			'lastname'  => $order['billing']['last_name'],
			'email'     => $order['billing']['email'],

			'phone' => $order['billing']['phone'],
//			'phone_mobile' => $order[''],
//			'fax' => $order[''],
			
			'notes' => null,
			'active' => 1,

//			'latitude' => $order[''],
//			'longitude' => $order[''],

			'state_id'   => $state_id,
			'country_id' => $country_id,
		];

        $address = Address::create($data);
        $customer->addresses()->save($address);

        $customer->update('invoicing_address_id' => $address->id);
        
        return $address;
    }
    
    public function createShippingAddress()
    {
        // Build Customer data
        $order = $this->raw_data;
        $customer = $this->customer;

		// Build Shipping address
        $name = $order['shipping']['company'] ? $order['shipping']['company'] : 
        		$order['shipping']['first_name'].' '.$order['shipping']['last_name'];

		$country    = $order['shipping']['country'];
		$country_id = null;
		$state      = $order['shipping']['state'];
		$state_id   = null;

		$scountry = \App\Country::findByIsoCode( $order['shipping']['country'] );
		if ($scountry) {
			$country    = $scountry->name;
			$country_id = $scountry->id;
		}

		$sstate = WooOrder::getState( $order['shipping']['state'], $order['shipping']['country'] );
		if ($sstate) {
			$state    = $sstate->name;
			$state_id = $sstate->id;
		}

		$data = [
			'alias' => $order['id'].'-Shipping',
			'webshop_id' => WooOrder::getShippingAddressId( $order ),

			'name_commercial' => $name,
			
			'address1' => $order['shipping']['address_1'],
			'address2' => $order['shipping']['address_2'],
			'postcode' => $order['shipping']['postcode'],
			'city'         => $order['shipping']['city'],
			'state_name'   => $state,
			'country_name' => $country,
			
			'firstname' => $order['shipping']['first_name'],
			'lastname'  => $order['shipping']['last_name'],
//			'email'     => $order['shipping']['email'],

//			'phone' => $order['shipping']['phone'],
//			'phone_mobile' => $order[''],
//			'fax' => $order[''],
			
			'notes' => null,
			'active' => 1,

//			'latitude' => $order[''],
//			'longitude' => $order[''],

			'state_id'   => $state_id,
			'country_id' => $country_id,
		];

        $address = Address::create($data);
        $customer->addresses()->save($address);

        $customer->update('shipping_address_id' => $address->id);
        
        return $address;
    }

    
    public static function getWooOrder( $order_id = 0 )
    {
        $oID = intval($order_id);

        if ( !($oID>0) ) {
        	return [];
        }

        // Do the Mambo!!!
		// Get Order fromm WooCommerce Shop
        try {

			$order = WooCommerce::get('orders/'.$oID);	// Array
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

		return $order;
    }

    public function tell_run_status() {
      return $this->run_status;
    }
    
    protected function logMessage($type, $msg)
    {
        $this->log[] = [$type, $msg];
    }
    
    public function logView()
    {
        return $this->log; 
    }
}