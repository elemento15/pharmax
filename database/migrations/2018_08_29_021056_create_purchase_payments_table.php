<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_payments', function(Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('purchase_order_id');
			$table->unsignedBigInteger('payment_id');
			$table->decimal('amount', 12, 4)->default(0);
			//$table->timestamps();

			$table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('restrict');
			$table->foreign('payment_id')->references('id')->on('payments')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('purchase_payments');
	}

}
