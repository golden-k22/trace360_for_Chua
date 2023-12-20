<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class RecipeMatrix extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
    ];
    use SoftDeletes;

    protected $fillable = [
    ];

    protected $dates = ['deleted_at'];

}
