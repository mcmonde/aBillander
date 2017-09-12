<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceListProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_list_product', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('price_list_id')->unsigned()->index();
			$table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');

			$table->integer('product_id')->unsigned()->index();
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

			$table->decimal('price', 20, 6)->default(0.0);
			
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
		Schema::drop('price_list_product');
	}

}
