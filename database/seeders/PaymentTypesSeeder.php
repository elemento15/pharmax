<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
			[
				'name' => 'Efectivo',
				'code' => 'EFE'
			],[
				'name' => 'Tarjeta de Débito',
				'code' => 'TD'
			],[
				'name' => 'Tarjeta de Crédito',
				'code' => 'TC'
			]
		];

		foreach ($types as $item) {
			PaymentType::create($item);
		}
    }
}
