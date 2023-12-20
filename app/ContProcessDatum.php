<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContProcessDatum extends Model
{
	use SoftDeletes;
	
	protected $fillable = 
	['fr_qp_id','process_value','quality_value',];

	protected $dates = ['deleted_at'];
}
