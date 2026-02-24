<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';
    protected $fillable = ['state_id', 'lgd_code', 'district_name'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
}
