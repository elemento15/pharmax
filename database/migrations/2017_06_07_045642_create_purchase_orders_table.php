<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_orders', function(Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('vendor_id');
			$table->datetime('order_date');
			$table->double('total')->default(0);
			$table->boolean('active')->default(1);
			$table->text('comments')->nullable();
			$table->timestamps();

			$table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('purchase_orders');
	}

}
