<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Important!
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employer extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password'
    ];

    protected $hidden = ['password'];

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
        return $this->hasOne(EmployerProfile::class, 'employer_id');
    }


    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'employer_skills',
            'employer_id',
            'skill_id'
        );
    }
}
