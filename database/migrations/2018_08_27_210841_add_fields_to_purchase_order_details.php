<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPurchaseOrderDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('purchase_order_details', function(Blueprint $table)
		{
			$table->double('subtotal')->default(0)->after('price');
			$table->decimal('iva', 5, 2)->default(0)->after('subtotal');
			$table->double('iva_amount')->default(0)->after('iva');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('purchase_order_details', function(Blueprint $table)
		{
			$table->dropColumn('iva');
			$table->dropColumn('subtotal');
			$table->dropColumn('iva_amount');
		});
	}

}
