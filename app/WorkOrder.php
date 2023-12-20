<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class WorkOrder extends Model implements Auditable
{


    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'description',
        'no_of_records',
        'prod_date',
        'remarks',
        'po_create',
    ];

    use SoftDeletes;

    protected $fillable = [
        'description',
        'no_of_records',
        'prod_date',
        'remarks',
    ];

    protected $dates = ['deleted_at'];

}
