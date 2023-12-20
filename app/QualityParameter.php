<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class QualityParameter extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'fr_measures',
        'fr_rec_flow_id',
        'value',
        'tolerance',
    ];
    use SoftDeletes;

    protected $fillable = [
        'fr_measures',
        'fr_rec_flow_id',
        'value',
        'tolerance',
    ];

    protected $dates = ['deleted_at'];

}
