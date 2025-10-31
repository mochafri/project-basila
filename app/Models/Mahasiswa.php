<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";
    protected $fillable = [
        "STUDENTID","FULLNAME","MASA_STUDI","PASS_CREDIT","GPA","STATUS","STUDYPROGRAMID","PREDIKAT"
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

    public function hitungStatus($studyPeriod, $sks, $ipk)
    {
        if ($studyPeriod <= 8 && $sks >= 110 && $ipk >= 3.01) {
            return 'Eligible';
        }
        return 'Tidak Eligible';
    }
}
