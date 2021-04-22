<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotization extends Model
{
    use HasFactory;

    protected $guarded = ['id','balance'];
	protected $appends = ['balance'];

	public function cotization_details()
	{
		return $this->hasMany('App\Models\CotizationDetail');
	}

	public function customer()
	{
		return $this->belongsTo('App\Models\Customer');
	}

	public function cotization_payments()
	{
		return $this->hasMany('App\Models\CotizationPayment');
	}

	public function getBalanceAttribute()
	{
		$paid = 0;
		
		if ($this->status != 'C') {
			foreach ($this->cotization_payments as $item) {
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
