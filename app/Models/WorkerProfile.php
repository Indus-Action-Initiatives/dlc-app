<?php

namespace App\Models;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkerProfile extends Model
{
    use HasFactory;

    protected $casts = [
        'skill_id' => 'array', // auto-convert JSON to PHP array
    ];

     protected $appends = ['profile_image_url','skill_names'];


    protected $fillable = [
        'worker_id',
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
        'plot_no',
        'street_area_village',
        'post_office',
        'district',
        'state',
        'pin_code',
        'profile_image',
        'gender',
        'docType',
        'docNumber',
        'pdf',
        'rate',
        'lat',
        'long'
    ];

   public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

     public function stateDetail()
    {
        return $this->belongsTo(State::class, 'state', 'lgd_code');
    }

    // ✅ Relationship with District
    public function districtDetail()
    {
        return $this->belongsTo(District::class, 'district', 'lgd_code');
    }


     // Custom accessor to get skill models
    public function getSkillsAttribute()
    {
        if (empty($this->skill_id)) {
            return collect();
        }

        return Skill::whereIn('id', $this->skill_id)->get();
    }


    public function getSkillNamesAttribute()
    {
        $ids = $this->skill_id ?? [];

        return \DB::table('skills')
            ->whereIn('id', $ids)
            ->pluck('name')
            ->toArray();
    }

    public function getProfileImageUrlAttribute()
    {
        // If no image stored
        if (empty($this->profile_image)) {
            return url('default/no-photos.png');
        }

        // Create full URL
        $path = env('UPLOAD_PATH_WORKER').'/profile/';
        return url($path . $this->profile_image);
    }


//      public function skill()
//     {
//         return $this->belongsTo(Skill::class, 'skill_id');
//     }
 }
