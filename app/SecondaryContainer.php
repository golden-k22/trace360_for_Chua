<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class SecondaryContainer extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
	'container_ref',
	'desc',


    ];

    protected $fillable=[
	'container_ref',
	'desc',

    ];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
