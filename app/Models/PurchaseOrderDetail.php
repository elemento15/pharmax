<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public function purchase_order()
	{
		return $this->belongsTo('App\Models\PurchaseOrder');
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
