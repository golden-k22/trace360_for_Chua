<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DeviceGrouping extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
	'fr_main_dev_id',
	'fr_sub_dev_id',
    ];

    use SoftDeletes;

    protected $fillable = [
        'fr_main_dev_id',
        'fr_sub_dev_id',
    ];

    protected $dates = ['deleted_at'];

}
