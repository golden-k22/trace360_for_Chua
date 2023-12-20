<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class IngredientRack extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'position',
        'max_weight',
        'fr_devices',
        'rack_label',
        'balance',
    ];
    use SoftDeletes;

    protected $fillable = [
        'rack_label',
        'position',
        'max_weight',
        'fr_devices',
    ];

    protected $dates = ['deleted_at'];
}
