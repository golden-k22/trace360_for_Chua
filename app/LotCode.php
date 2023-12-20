<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class LotCode extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'fr_ing_id',
        'lot_code',
        'expiry_date',
        'qty',
        'fr_measures',
        'balance',
        'daystoexpiry',
        'fr_lotcode_flag',
    ];
    use SoftDeletes;

    protected $fillable = [
        'fr_ing_id',
        'lot_code',
        'expiry_date',
        'qty',
        'fr_measures',
        'balance',
        'daystoexpiry',
        'fr_lotcode_flag',
    ];

    protected $dates = ['deleted_at'];

}
