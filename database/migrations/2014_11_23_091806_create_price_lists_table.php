<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('price_lists');

		Schema::create('price_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 32)->nullable(false);


			$table->string('type', 32)->nullable(false);	// 'price' -> amount; 'discount' - > percent of discount: 'margin' -> percent of margin

			$table->tinyInteger('price_is_tax_inc')->default(0);	// Price is tax included (VAT)? (only if type = 0)
			$table->decimal('amount', 20, 6)->default(0.0);			// Amount if type = 1,2

			$table->integer('currency_id')->unsigned()->nullable(false);
			
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('price_lists');
	}

}
