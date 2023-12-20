<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Translatable;
use OwenIt\Auditing\Contracts\Auditable;

class RecipeFlow extends Model implements Auditable
{


    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'fr_rec_id',
        'process_order',
        'qty',
        'previous_step',
        'next_step',
        'duration',
        'fr_process_step',
        'fr_measure',
        'fr_time_param',
        'queue',
        'desc_test',
    ];

    protected $fillable = [
        'fr_rec_id',
        'process_order',
        'qty',
        'previous_step',
        'next_step',
        'duration',
        'fr_process_step',
        'fr_measure',
        'fr_time_param',
        'queue',
        'desc_test',
    ];

    use SoftDeletes;
    use Translatable;
    protected $table = 'recipe_flows';
    protected $dates = ['deleted_at'];
    //protected $with = ['unit'];

    /*public function unit()
    {
        return $this->hasOne('App\Measure');
    }*/

}