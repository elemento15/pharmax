<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorPricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vendor_prices', function(Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('vendor_id');
			$table->unsignedBigInteger('product_id');
			$table->double('price')->default(0);
			$table->timestamps();

			$table->unique(['vendor_id', 'product_id']);
			$table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vendor_prices');
	}

}
