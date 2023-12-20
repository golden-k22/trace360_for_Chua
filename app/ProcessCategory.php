<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ProcessCategory extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'name',
    ];
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $date = ['deleted_at'];
}
