<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'job_post_id',
        'employer_id',
        'status',
        'note'
    ];

    // Relations
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function job()
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

}
