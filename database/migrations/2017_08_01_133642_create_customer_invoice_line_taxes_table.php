<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerInvoiceLineTaxesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_invoice_line_taxes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 64)->nullable(false);

			$table->decimal('taxable_base', 20, 6)->default(0.0);							// Base for tax calculations
			$table->decimal('percent', 8, 3)->default(0.0);								// Tax percent
			$table->decimal('amount', 20, 6)->default(0.0);								// Tax may be fixed amount

			$table->integer('customer_invoice_line_id')->unsigned()->nullable(false);
			$table->integer('tax_id')->unsigned()->nullable(false);							// ToDo: set relationship to Tax model
			$table->integer('tax_rule_id')->unsigned()->nullable(false);					// What if it changes/disappears??

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
		Schema::drop('customer_invoice_line_taxes');
	}

}
