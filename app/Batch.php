<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Batch extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'batch_name',
    ];

    use SoftDeletes;

    protected $fillable = [
        'batch_name',
    ];

    protected $dates = ['deleted_at'];

}
