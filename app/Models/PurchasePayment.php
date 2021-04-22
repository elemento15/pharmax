<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public $timestamps = false;

	public function purchase_order()
	{
		return $this->belongsTo('App\Models\PurchaseOrder');
	}

	public function payment()
	{
		return $this->belongsTo('App\Models\Payment');
	}
}
