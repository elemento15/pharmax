<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public function purchase_order_details()
	{
		return $this->hasMany('App\Models\PurchaseOrderDetail');
	}

	public function scopeSetWorked($query)
	{
		$this->worked = 1;
		$this->save();
	}
}
