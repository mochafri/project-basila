<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";
    protected $fillable = [
        "nim", "name", "study_period", "pass_sks", "ipk",
        "predikat", "status", "alasan_status", "status_otomatis"
    ];

    protected static function booted()
    {
        static::saving(function ($mahasiswa) {

            $mahasiswa->predikat = $mahasiswa->hitungPredikat($mahasiswa->ipk);

            $mahasiswa->status_otomatis = $mahasiswa->hitungStatus(
                $mahasiswa->study_period,
                $mahasiswa->pass_sks,
                $mahasiswa->ipk
            );
        });
    }

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

    public function hitungStatus($studyPeriod, $sks, $ipk)
    {
        if ($studyPeriod <= 8 && $sks >= 110 && $ipk >= 3.01) {
            return 'Eligible';
        }
        return 'Tidak Eligible';
    }
}
