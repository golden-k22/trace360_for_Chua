<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;
use OwenIt\Auditing\Contracts\Auditable;

class PhysicalDeviceRecipeFlow extends Model implements Auditable
{


    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'recipeflow_id',
        'physicaldevice_id',
        'cycle_time',
        'lapse_time',
        'qty_per_ctn',
        'ctn_x_count',
        'ctn_y_count',
        'ctn_z_count',
        'pack_qty',
        'pack_material',
    ];

    protected $fillable = [
        'recipeflow_id',
        'physicaldevice_id',
        'cycle_time',
        'lapse_time',
        'qty_per_ctn',
        'ctn_x_count',
        'ctn_y_count',
        'ctn_z_count',
        'pack_qty',
        'pack_material',
    ];

    use SoftDeletes;
    use Translatable;
    protected $table = 'physicaldevice_recipeflow';
    protected $dates = ['deleted_at'];
    //protected $with = ['unit'];

    /*public function unit()
    {
        return $this->hasOne('App\Measure');
    }*/

}