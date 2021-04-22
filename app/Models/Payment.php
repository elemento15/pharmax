<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public function type()
	{
		return $this->belongsTo('App\Models\PaymentType', 'payment_type_id');
	}

	public function cotization_payments()
	{
		return $this->hasMany('App\Models\CotizationPayment');
	}

	public function purchase_payments()
	{
		return $this->hasMany('App\Models\PurchasePayment');
	}
}
