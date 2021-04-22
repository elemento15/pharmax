<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id','balance'];
	protected $appends = ['balance'];

	public function purchase_order_details()
	{
		return $this->hasMany('App\Models\PurchaseOrderDetail');
	}

	public function vendor()
	{
		return $this->belongsTo('App\Models\Vendor');
	}

	public function purchase_payments()
	{
		return $this->hasMany('App\Models\PurchasePayment');
	}

	public function getBalanceAttribute()
	{
		$paid = 0;
		
		if ($this->status != 'C') {
			foreach ($this->purchase_payments as $item) {
				if ($item->payment->active) {
					$paid += $item->amount;
				}
			}

			return $this->total - $paid;
		} else {
			return 0;
		}
	}
}
