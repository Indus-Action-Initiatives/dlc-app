<?php

namespace App\Models;

use App\Models\Skill;
use App\Models\State;
use App\Models\District;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobPost extends Model
{
    use HasFactory;

    protected $fillable = [
    'employer_id',
    'title',
    'description',
    'skills_id',        // array ko JSON me store karenge
    'address',
    'qualification',
    'rate',
    'job_type',
    'status',           // default: open
    'tools_required',
    'required_people',
    'work_time',
    'work_location',
    'work_duration',
    'lat',
    'long',
    'start_date',
    'end_date',
    'state',
    'district',
    'pincode',
    'work_duration',
    'address'
];

   // protected $appends = ['location'];
     protected $appends = ['location', 'skills_list'];

    public function getLocationAttribute()
    {
        return [
            'lat' => $this->lat,
            'long' => $this->long
        ];
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'job_post_skill',
            'job_post_id',
            'skill_id'
        );
    }

    public function facilities()
    {
        return $this->belongsToMany(
            Facility::class,
            'job_post_facilities',
            'job_post_id',
            'facility_id'
        );
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'lgd_code');
    }

    /** -----------------------------
     *  Correct District Relationship
     *  ----------------------------- */
    public function district()  // changed name to avoid conflict
    {
        return $this->belongsTo(District::class, 'district', 'lgd_code');
    }

     public function applicants()
    {
        return $this->hasMany(JobApplication::class, 'job_post_id');
    }


        public function getSkillsListAttribute()
        {
            $ids = json_decode($this->skills_id, true);

            if (!$ids) {
                return [];
            }

            return Skill::whereIn('id', $ids)->get();
        }
}
