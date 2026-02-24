<?php

namespace App\Models;

use App\Models\District;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Important

class Worker extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'password'
    ];

    protected $hidden = ['password'];

    // JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relation with profile
    public function profile()
    {
        return $this->hasOne(WorkerProfile::class);
    }

 

}
