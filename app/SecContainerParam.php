<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SecContainerParam extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
	'fr_rec_id',
	'fr_device_recflow_id',
	'qty_per_ctn',
	'ctn_x_count',
	'ctn_y_count',
	'ctn_z_count',
    ];
    use SoftDeletes;

    protected $fillable = [
        'fr_rec_id',
        'fr_device_recflow_id',
        'qty_per_ctn',
        'ctn_x_count',
        'ctn_y_count',
        'ctn_z_count',
    ];

    protected $dates = ['deleted_at'];



}
