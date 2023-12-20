<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Recipe extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'name',
        'barcode',
        'sequen',
        'fr_batches',
    ];

    protected $fillable=[
        'name',
        'barcode',
        'sequen',
        'fr_batches',
    ];


    use SoftDeletes;

    protected $table = 'recipes';

    protected $dates = ['deleted_at'];

    public function items()
    {
        return $this->hasMany('App\RecipeFlow');
    }

}