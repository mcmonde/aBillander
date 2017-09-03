<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addresses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('alias', 32)->nullable(false);
			$table->string('webshop_id', 16)->nullable();			// Address's Web Shop id
			$table->string('model_name', 64)->nullable(false);    	// Address may be owned by a Supplier, Manufacturer, warehouse...!
																	// http://www.colorfultyping.com/single-table-inheritance-in-laravel-4/

			$table->string('name_commercial', 64)->nullable();
			
			$table->string('address1', 128)->nullable(false);
			$table->string('address2', 128)->nullable();
			$table->string('postcode', 12)->nullable();
			$table->string('city', 64)->nullable();
			$table->string('state', 64)->nullable();
			$table->string('country', 64)->nullable();
			
			$table->string('firstname', 32)->nullable();			// Contact information
			$table->string('lastname', 32)->nullable();
			$table->string('email', 128)->nullable();

			$table->string('phone', 32)->nullable();
			$table->string('phone_mobile', 32)->nullable();
			$table->string('fax', 32)->nullable();
			
			$table->text('notes')->nullable();
			$table->tinyInteger('active')->default(1);

			$table->float('latitude')->nullable();					// Geolocation
			$table->float('longitude')->nullable();
			
			$table->integer('owner_id')->unsigned()->nullable(false);	// Address may be owned by a Supplier, Manufacturer, warehouse...!
			$table->integer('state_id')->unsigned()->nullable();	// Use if defined
			$table->integer('country_id')->unsigned()->nullable();	// Use if defined. See https://www.youtube.com/watch?v=ohDAdZqEyjk
			
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('addresses');
	}

}
