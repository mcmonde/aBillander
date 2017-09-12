<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tax_rules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('country_id', 64)->nullable();
			$table->string('state_id', 64)->nullable();
			$table->tinyInteger('sales_equalization')->default(0);		// Apply "Recargo de Equivalencia" (sales equalization tax in Spain and Belgium only). Vendors must charge these customers a sales equalization tax in addition to output tax. 

			$table->string('name', 64)->nullable(false);
			$table->decimal('percent', 8, 3)->default(0.0);
			$table->decimal('amount', 20, 6)->default(0.0);				// Tax may be fixed amount

			$table->integer('position')->unsigned()->default(0);		// Taxes apply according to this order number (lowest applies first)
			
			$table->integer('tax_id')->unsigned()->default(0);

			$table->timestamps();
			$table->softDeletes();

			$table->foreign('tax_id')
			      ->references('id')
			      ->on('taxes')
			      ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tax_rules');
	}

}
