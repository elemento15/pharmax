<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cotizations', function(Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('customer_id');
			$table->datetime('cotization_date');
			$table->double('total')->default(0);
			$table->unsignedBigInteger('status_id')->nullable();
			$table->boolean('active')->default(1);
			$table->text('comments')->nullable();
			$table->timestamps();

			$table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
			$table->foreign('status_id')->references('id')->on('statuses')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cotizations');
	}

}
