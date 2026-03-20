<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerSkill extends Model
{
    use HasFactory;

     protected $table = 'employer_skills';   // <-- table name define

    protected $fillable = [
        'employer_id',
        'skill_id',
    ];

     // EmployerSkill → Employer
    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    // EmployerSkill → Skill
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }

}
