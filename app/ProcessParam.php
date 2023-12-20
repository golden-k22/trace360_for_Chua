<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class ProcessParam extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
	'fr_rec_flow_id',
	'fr_sensor_id',
	'value',
	'tolerance',
	'fr_measures',
	'value_2',
	'tolerance_2',
	'fr_measures2',
	'value_3',
	'tolerance_3',
	'fr_measures3',
	'duration',
	'fr_time_param',
	'para_constant',
	'value_order',
	'job_type',
    ];
    use SoftDeletes;

    protected $fillable = [
        'fr_rec_flow_id',
        'fr_sensor_id',
        'value',
        'tolerance',
        'fr_measures',
        'value_2',
        'tolerance_2',
        'fr_measures2',
        'value_3',
        'tolerance_3',
        'fr_measures3',
        'duration',
        'fr_time_param',
        'para_constant',
        'value_order',
		'job_type',
     ];

    protected $dates = ['deleted_at'];

}
