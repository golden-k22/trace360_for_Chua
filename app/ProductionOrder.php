<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductionOrder extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'fr_rec_list_id',
		'qty',
        'rec_order',
		'batch',
		'sequen',
    ];
    
    protected $dates = ['deleted_at'];    
}
