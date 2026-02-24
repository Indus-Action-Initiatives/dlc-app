<?php

namespace App\Models;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkerEducation extends Model
{
    use HasFactory;
    protected $table = 'worker_educations';

    protected $fillable = [
        'worker_id',
        'education_level',
        'institution',
        'passing_year',
        'certificate_path',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
