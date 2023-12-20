<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class IngredientList extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'fr_rec_id',
        'fr_ing_id',
        'qty',
        'ing_order',
        'fr_measures',
    ];
    use SoftDeletes;

    protected $fillable = [
        'qty',
        'ing_order',
        'fr_rec_id',
        'fr_ing_id',
        'fr_measures',
    ];

    protected $dates = ['deleted_at'];

}
