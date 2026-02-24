<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'age',
        'name',
        //'skill_id',
        'avg_worker',
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
        'emp_type'
    ];

    protected $appends = ['profile_image_url'];

   public function worker()
    {
        return $this->belongsTo(Employer::class);
    }

     public function getProfileImageUrlAttribute()
    {
        // If no image stored
        if (empty($this->profile_image)) {
            return url('default/no-photos.png');
        }

        // Create full URL
        $path = env('UPLOAD_PATH_EMPLOYER').'/profile/';
        return url($path . $this->profile_image);
    }
}
