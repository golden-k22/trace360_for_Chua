<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class User extends \TCG\Voyager\Models\User implements Auditable
{


    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'avatar',
        'role_id',
        'settings',
        'barcode',
	'plc_pin',
    ];
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'barcode', 'plc_pin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function PhysicalDevices()
    {
        return $this->belongsToMany(PhysicalDevice::class,'physicaldevice_user','user_id','physicaldevice_id');
    }

}
