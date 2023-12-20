<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class ProductionParam extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
	'f_rec_flow_id',
	'f_sensor_id',
	'value',
	'tolerance',
	'f_tol_measures',
	'value2',
	'tolerance2',
	'f_tol_measures2',
	'value3',
	'tolerance3',
	'f_tol_measures3',
	'value_order',
    ];

    use SoftDeletes;

    protected $fillable = [
        'f_rec_flow_id',
        'f_sensor_id',
        'value',
        'tolerance',
        'f_tol_measures',
        'value2',
        'tolerance2',
        'f_tol_measures2',
        'value3',
        'tolerance3',
        'f_tol_measures3',
        'value_order',
    ];

    protected $dates = ['deleted_at'];

}
