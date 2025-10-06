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

    public function getPredikat($gpa)
    {
        if ($gpa >= 3.51)
            return 'Cumlaude';
        if ($gpa >= 3.00)
            return 'Sangat Memuaskan';
        if ($gpa >= 2.75)
            return 'Memuaskan';
        return 'Cukup';
    }
}