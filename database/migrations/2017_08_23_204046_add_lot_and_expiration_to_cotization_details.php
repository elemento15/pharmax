<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotAndExpirationToCotizationDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cotization_details', function(Blueprint $table)
		{
			$table->string('expiration')->nullable()->after('total');
			$table->string('lot')->nullable()->after('total');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cotization_details', function(Blueprint $table)
		{
			$table->dropColumn('expiration');
			$table->dropColumn('lot');
		});
	}

}
