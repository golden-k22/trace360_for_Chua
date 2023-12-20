<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SensorDeviceMapping extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
	'fr_sensor_id',
	'fr_device_id',
	'sensor_order',
	'param_editable',
	'data_store',
	'data_coll_interval',
    ];
    use SoftDeletes;

    protected $fillable = [
        'fr_sensor_id',
        'fr_device_id',
        'sensor_order',
        'param_editable',
        'data_store',
        'data_coll_interval',
    ];

    protected $dates = ['deleted_at'];



}
