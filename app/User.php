<?php

namespace App;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Upload;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles, SoftDeletes, CascadesDeletes;
    protected $cascadeDeletes  = ['userDetail', 'upload'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // Protected $guard_name ='api';
    // protected $appends = ['allPermissions'];
    protected $fillable = [
        'name', 'salutation', 'email', 'confirm', 'password',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'confirm'
    ];

    // /**
    //  * Generate random password
    //  */
    // public static function generatePassword($length = 32)
    // {
    //     return bcrypt(str_random($length));
    // }

    public function setPasswordAttribute($val)
    {
        $this->attributes['password'] =  $val ? bcrypt($val) : null;
    }
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
