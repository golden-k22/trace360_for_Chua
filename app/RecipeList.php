<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class RecipeList extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'fr_wo_id',
        'fr_rec_id',
        'no_of_batch',
    ];
    use SoftDeletes;

    protected $fillable = [
        'fr_wo_id',
        'fr_rec_id',
        'no_of_batch',
    ];

    protected $dates = ['deleted_at'];
}
