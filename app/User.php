<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = "users";

    public function sales()
    {
        return $this->hasMany(SaleRecord::class, 'mobile_user_id', 'id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class);
    }

    public function getAuthPassword()
    {
        return $this->encrypted_password;
    }

    public function getPasswordAttribute()
    {
        return $this->encrypted_password;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['encrypted_password'] = $password;
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
}
