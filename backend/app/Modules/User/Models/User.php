<?php

namespace App\Modules\User\Models;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use DesignMyNight\Mongodb\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    // use User:insert() or DB::collection('users')->insert()
    // DB::connection('mongodb')->collection('users')->insert()
    //to insert any data not in $fillable array
    use Notifiable, SoftDeletes, HasApiTokens;

//    protected $connection = 'mongodb';

    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role_id',
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Set password attribute
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // auto make user active on creating
            if (! isset($model->status)) {
                $model->status = config('constants.USER.STATUS.ACTIVE');
            }
        });
    }

    public static function firstAdmin($fields = ['*'])
    {
        return static::where('role_id', config('constants.USER.ROLE.ADMIN'))
            ->first($fields);
    }
}
