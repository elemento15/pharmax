<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusToCotizations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cotizations', function(Blueprint $table)
		{
			// delete "status_id" field
			$table->dropForeign('cotizations_status_id_foreign');
			$table->dropColumn('status_id');

			// add new status "field"
			$table->enum('status', ['N', 'P', 'C'])->default('N')->after('total');
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
			$table->unsignedBigInteger('status_id')->nullable()->after('total');
			$table->foreign('status_id')->references('id')->on('statuses')->onDelete('restrict');

			$table->dropColumn('status');
		});
	}

}
