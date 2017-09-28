<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerInvoiceLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_invoice_lines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('line_sort_order')->nullable();						// To sort lines 
			$table->integer('line_type')->nullable();							// Producto, Comentario, Servicio, Suplido, Transporte???

			$table->integer('product_id')->unsigned()->nullable();
			$table->integer('combination_id')->unsigned()->nullable();
			$table->string('reference', 32)->nullable();
			$table->string('name', 128)->nullable(false);
			$table->decimal('quantity', 20, 6);

			$table->decimal('cost_price', 20, 6)->default(0.0);
			$table->decimal('unit_price', 20, 6)->default(0.0);					// From Product data (initial price)
			$table->decimal('unit_customer_price', 20, 6)->default(0.0);		// Calculated custom for customer (initial price for customer)
																				//  '-> Should not be modified on order entry. Apply discount instead
			$table->decimal('unit_final_price', 20, 6)->default(0.0);			// Just if you allow to modify customer price

			$table->decimal('unit_net_price', 20, 6)->default(0.0);				// unit_net_price = unit_final_price - discount

			$table->decimal('discount_percent', 8, 3)->default(0.0);			// Not the same as discount amount!! Maybe both applies!
			$table->decimal('discount_amount_tax_incl', 20, 6)->default(0.0);	// Line discount refered to Customer Price
			$table->decimal('discount_amount_tax_excl', 20, 6)->default(0.0);

			$table->decimal('total_tax_incl', 20, 6)->default(0.0);
			$table->decimal('total_tax_excl', 20, 6)->default(0.0);

			$table->decimal('tax_percent', 8, 3)->default(0.0);					// Tax percent
			$table->decimal('commission_percent', 8, 3)->default(0.0);			// Commission percent

			$table->text('notes')->nullable();
			
			$table->tinyInteger('locked')->default(0);							// 0 -> NO; 1 - > Yes (line is after a shipping-slip => should not mofify quantity).

			$table->integer('customer_invoice_id')->unsigned()->nullable(false);
			$table->integer('tax_id')->unsigned()->nullable(false);
			$table->integer('sales_rep_id')->unsigned()->nullable();             // Sales representative

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
		Schema::drop('customer_invoice_lines');
	}

}
