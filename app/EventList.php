<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EventList extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'event_cat',
        'event_sub_cat',
        'event_type',
        'description',
    ];

	use SoftDeletes;
	
	protected $fillable = [
		'event_cat',
		'event_sub_cat',
		'event_type',
		'description',
	];

	protected $dates = ['deleted_at'];
    
}
