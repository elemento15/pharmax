<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizationDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public function cotization()
	{
		return $this->belongsTo('App\Models\Cotization');
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
