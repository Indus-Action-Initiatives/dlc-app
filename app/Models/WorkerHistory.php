<?php

namespace App\Models;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkerHistory extends Model
{
    use HasFactory;

       protected $casts = [
        'skill_id' => 'array', // auto-convert JSON to PHP array
    ];

    protected $appends = ['skills'];

    protected $fillable = [
        'worker_id',
        'employer_id',
        'employer_name',
        'project_name',
        'work_place',
        'work_type',
        'skill_id',
        'task_description',
        'start_date',
        'end_date',
        'days_worked',
        'daily_wage',
        'total_amount',
        'payment_status',
        'remarks',
        'rating'
    ];


    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

     public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

  public function getSkillsAttribute()
    {
        if (empty($this->skill_id)) {
            return collect();
        }

        // Make sure skill_id is an array
        $skillIds = is_array($this->skill_id) ? $this->skill_id : json_decode($this->skill_id, true);

        return Skill::whereIn('id', $skillIds)->get();
    }
}
