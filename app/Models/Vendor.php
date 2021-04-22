<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $guarded = ['id','balance'];
	protected $appends = ['balance'];

	public function getBalanceAttribute()
	{
		$paid = 0;
		$orders = PurchaseOrder::where('vendor_id', $this->id)
		                       ->where('status', 'N')
		                       ->get();

		foreach ($orders as $key => $item) {
			$paid += $item->balance;
		}

		return $paid;
	}
}
