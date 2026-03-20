<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 🔗 Relation with JobPost (many-to-many)
    public function jobPosts()
    {
        return $this->belongsToMany(JobPost::class, 'job_post_skill', 'skill_id', 'job_post_id');
    }

    public function employers()
    {
        return $this->belongsToMany(
            Employer::class,
            'employer_skills',
            'skill_id',
            'employer_id'
        );
    }
}
