<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

   protected $fillable = [
        'user_id',
        'name',
        'age',
        'skill_id',
        'experience',
        'work_type',
        'location',
        'availability',
        'eshram',
        'aadhar',
        'bocw',
        'language',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
