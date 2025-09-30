<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MhsYud extends Model
{

    use HasFactory;
    protected $table = 'mhs_yudiciums';

    protected $fillable = [
        'nim',
        'fakultas_id',
        'prody_id',
        'name',
        'study_period',
        'pass_sks',
        'ipk',
        'predikat',
        'status',
        'yudicium_id'
    ];

    public function hitungPredikat($ipk)
    {
        if ($ipk >= 3.51){
            return 'Very Good (Sangat Memuaskan)';
        }
        if ($ipk >= 3.01){
            return 'Good (Memuaskan)';
        }
        return 'Fair (Cukup)';
    }
}
