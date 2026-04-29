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
        'id_smt_masuk',
        'fakultas_id',
        'prody_id',
        'name',
        'tmp_lahir',
        'tgl_lahir',
        'study_period',
        'pass_sks',
        'ipk',
        'predikat',
        'status',
        'yudicium_id'
    ];

    public function getPredikat($gpa)
    {
        if ($gpa == 4.00)
            return 'Sempurna (Summa Cumlaude)';
        elseif ($gpa >= 3.51 && $gpa < 4.00)
            return 'Dengan Pujian (Cumlaude)';
        elseif ($gpa >= 3.00)
            return 'Sangat Memuaskan (Very Good)';
        elseif ($gpa >= 2.75)
            return 'Memuaskan (Good)';
        else
            return 'Tanpa Predikat';
    }

    public function yudicium()
    {
        return $this->belongsTo(Yudicium::class, 'yudicium_id');
    }
}