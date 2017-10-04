<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name_fiscal', 128)->nullable();						// Company
			$table->string('name_commercial', 64)->nullable();

			$table->string('website', 128)->nullable();
			
			$table->string('identification', 64)->nullable();					// VAT ID or the like (only companies & pro's?)
			$table->string('webshop_id', 16)->nullable();

			$table->string('payment_days', 16)->nullable();			// Comma separated integuers!
			$table->tinyInteger('no_payment_month')->default(0);
			
			$table->decimal('outstanding_amount_allowed', 20, 6)->default(0.0);	// Maximum outstanding amount allowed
			$table->decimal('outstanding_amount', 20, 6)->default(0.0);	        // Actual balance
			$table->decimal('unresolved_amount', 20, 6)->default(0.0);	        // Uncertain Payment

			$table->text('notes')->nullable();
			$table->tinyInteger('sales_equalization')->default(0);				// Charge Sales equalization tax? (only Spain)
			$table->tinyInteger('allow_login')->default(0);						// Allow login to Customer Center
			$table->tinyInteger('accept_einvoice')->default(0);					// Accept electronic invoice
			$table->tinyInteger('blocked')->default(0);							// Sales not allowed
			$table->tinyInteger('active')->default(1);
			
			$table->integer('sales_rep_id')->unsigned()->nullable();             // Sales representative
			$table->integer('currency_id')->unsigned()->nullable();
			$table->integer('language_id')->unsigned()->nullable();
			$table->integer('customer_group_id')->unsigned()->nullable();
			$table->integer('payment_method_id')->unsigned()->nullable();
			$table->integer('sequence_id')->unsigned()->nullable();
			$table->integer('invoice_template_id')->unsigned()->nullable();
			$table->integer('carrier_id')->unsigned()->nullable();
			$table->integer('price_list_id')->unsigned()->nullable();
			$table->integer('direct_debit_account_id')->unsigned()->nullable(); // Cuenta remesas

			$table->integer('invoicing_address_id')->unsigned()->nullable(false);
			$table->integer('shipping_address_id')->unsigned()->nullable();
			
			$table->string('secure_key', 32)->nullable(false);					// = md5(uniqid(rand(), true))
			
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
		Schema::drop('customers');
	}

}
