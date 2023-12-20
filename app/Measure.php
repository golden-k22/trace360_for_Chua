<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Measure extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'unit_name',
        'symbol',
        'queue',
		'grouping',
		'factor'
    ];
    use SoftDeletes;
    protected $table = 'measures';


    protected $dates = ['deleted_at'];

	public function scopeLotcodegroup($query)
	{
		return $query->whereIn('grouping',[5]);
	}
	/*2021-11-17
	"1": "Time",
	"2": "Capacity",
	"3": "Weigh",
	"4": "Dimension",
	"5": "Item",
    "6": "Thermal",
    "7": "Solid",
    "8": "Rate of Change"*/
	public function scopeIngredientgroup($query)
	{
		return $query->whereIn('grouping',[2,3,5,7]);
	}

	public function scopeRecipeflowsgroup($query)
	{
		return $query->whereIn('grouping',[3,5]);
	}

	public function scopeIngredientlistsgroup($query)
	{
		return $query->whereIn('grouping',[2,3,5,7]);
	}

}
