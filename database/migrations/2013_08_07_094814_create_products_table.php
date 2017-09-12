<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('product_type', array('simple', 'virtual', 'combinable', 'grouped'))->default('simple');
			// 'simple'     => a collection of related products that can be purchased individually and only 
							// consist of simple products. Simple products are shipped and have no combitions.
			// 'virtual'    => one that doesn’t require shipping or stock management (Services, downloads...).
			// 'combinable' => a product with combitions, each of which may have a different SKU, price, stock 
							// option, etc.
			// 'grouped'    => a collection of related products that can be purchased individually and only 
							// consist of simple products. 

			$table->string('name', 128)->nullable(false);
			$table->string('reference', 32)->nullable();
			$table->string('ean13', 13)->nullable();
			$table->text('description')->nullable();
			$table->text('description_short')->nullable();
			$table->string('measure_unit', 32)->nullable();
			$table->tinyInteger('quantity_decimal_places')->unsigned()->default(0);

			$table->decimal('quantity_onhand', 20, 6)->default(0);			// In Stock; on hand
			$table->decimal('quantity_onorder', 20, 6)->default(0);			// On order; Quantity on Purchase Orders (pendiente de recibir)
			$table->decimal('quantity_allocated', 20, 6)->default(0);		// Allocated; Quantity on Sale Orders (reservado)
			$table->decimal('quantity_onorder_mfg', 20, 6)->default(0);		// On order (manufacturing); Quantity on Production Orders
			$table->decimal('quantity_allocated_mfg', 20, 6)->default(0);	// Allocated (manufacturing); Quantity allocated for Production Orders
																			// Available: instock + onorder - allocated

			$table->decimal('reorder_point', 20, 6)->default(0);			// Acts like safety stock or minimum stock
			$table->decimal('maximum_stock', 20, 6)->default(0);	
			
			$table->decimal('price', 20, 6)->default(0.0);
			$table->decimal('last_purchase_price', 20, 6)->default(0.0);
			$table->decimal('cost_price', 20, 6)->default(0.0);
			$table->decimal('cost_average', 20, 6)->default(0.0);			// Should be on per warehouse base?

			$table->string('supplier_reference', 32)->nullable();
			$table->integer('supply_lead_time')->unsigned()->default(0);

			$table->string('location', 64)->nullable();
			$table->decimal('width', 20, 6)->nullable()->default(0.0);   // cm
			$table->decimal('height', 20, 6)->nullable()->default(0.0);
			$table->decimal('depth', 20, 6)->nullable()->default(0.0);
			$table->decimal('weight', 20, 6)->nullable()->default(0.0);  // kg

			$table->integer('warranty_period')->unsigned()->default(0);  // days

			// ToDo: barcode / barcode type, supplier(s) data (currency, price, supplier reference, lead-time)
			
			$table->text('notes')->nullable();
			$table->tinyInteger('stock_control')->default(1);
			$table->tinyInteger('publish_to_web')->default(0);
			$table->tinyInteger('blocked')->default(0);							// Sales not allowed
			$table->tinyInteger('active')->default(1);
			
			$table->integer('tax_id')->unsigned()->nullable(false);
			$table->integer('category_id')->unsigned()->nullable();
			$table->integer('main_supplier_id')->unsigned()->nullable();
			
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
		Schema::drop('products');
	}

}
