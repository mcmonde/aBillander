<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('reference')->nullable();			// Creditor (usually Supplier) reference			
			$table->string('name', 32)->nullable();		// Payment subject/detail

			$table->date('due_date')->nullable(false);
			$table->date('payment_date')->nullable();			// Real payment date
			$table->decimal('amount', 20, 6)->nullable(false);
	//		$table->decimal('amount_paid', 20, 6)->nullable();

			$table->integer('currency_id')->unsigned()->nullable(false);
			$table->decimal('currency_conversion_rate', 20, 6)->default(1.0);

			$table->enum('status', array('pending', 'bounced', 'paid'))->default('pending');

			$table->text('notes')->nullable();

			$table->integer('invoice_id')->unsigned()->nullable(false);		// Customer / Supplier document (Invoice, Credit Note or Debit Note)
			$table->string('model_name', 64)->nullable(false);  // Payment may be owned by a CustomerInvoice, SupplierInvoice...!
			$table->integer('owner_id')->unsigned()->nullable(false);		// Customer or Supplier
			$table->string('owner_model_name', 64)->nullable(false);        // Payment may be owned by a Customer or Supplier

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
		Schema::drop('payments');
	}

}
