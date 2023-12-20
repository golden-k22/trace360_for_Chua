<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Ingredient extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'name',
        'upper_tol',
        'lower_tol',
        'barcode',
        'qty_p_pack',
        'fr_measures',
        'remarks',
    ];
    use SoftDeletes;

    protected $fillable = [
        'name',
        'upper_tol',
        'lower_tol',
        'barcode',
        'qty_p_pack',
        'fr_measures',
        'remarks',
    ];

    protected $dates = ['deleted_at'];

}
