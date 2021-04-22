<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCotizations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cotizations', function(Blueprint $table)
		{
			$table->double('subtotal')->default(0)->after('cotization_date');
			$table->double('iva_amount')->default(0)->after('subtotal');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cotizations', function(Blueprint $table)
		{
			$table->dropColumn('subtotal');
			$table->dropColumn('iva_amount');
		});
	}

}
