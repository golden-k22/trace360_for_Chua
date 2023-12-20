<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PhysicalDevice extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'mac_id',
        'name',
        'ip_address',
        'location',
        'device_icon',
        'fr_proc_id',
        'israck',
	'is_main_ctrl',
	'is_sec_pack',
	'mac_idd',
    ];
    use SoftDeletes;

    protected $fillable = [
        'mac_id',
        'name',
        'ip_address',
        'location',
        'device_icon',
        'fr_proc_id',
        'israck',
	'is_main_ctrl',
	'is_sec_pack',
	'mac_idd',
    ];

    protected $dates = ['deleted_at'];

    public function Users()
    {
        return $this->belongsToMany(User::class,'physicaldevice_user','physicaldevice_id','user_id');
    }

    public function scopeIsrack($query)
    {
        return $query->where('israck', '2');
    }
    //1 = none, 2 = yes

}
