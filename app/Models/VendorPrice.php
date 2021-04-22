<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPrice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public function vendor()
	{
		return $this->belongsTo('App\Models\Vendor');
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
