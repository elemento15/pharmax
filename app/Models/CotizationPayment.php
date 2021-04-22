<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizationPayment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

	public $timestamps = false;

	public function cotization()
	{
		return $this->belongsTo('App\Models\Cotization');
	}

	public function payment()
	{
		return $this->belongsTo('App\Models\Payment');
	}
}
