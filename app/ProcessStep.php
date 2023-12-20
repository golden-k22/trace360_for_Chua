<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class ProcessStep extends Model implements Auditable
{


    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'process_name',
        'process_categories',
        'group',
        'manualable',
    ];
    use SoftDeletes;

    protected $fillable = [
        'process_name',
        'process_categories',
        'group',
    ];

    protected $dates = ['deleted_at'];
}
