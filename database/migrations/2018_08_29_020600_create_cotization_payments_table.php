<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizationPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cotization_payments', function(Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('cotization_id');
			$table->unsignedBigInteger('payment_id');
			$table->decimal('amount', 12, 4)->default(0);
			//$table->timestamps();

			$table->foreign('cotization_id')->references('id')->on('cotizations')->onDelete('restrict');
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
		Schema::drop('cotization_payments');
	}

}
