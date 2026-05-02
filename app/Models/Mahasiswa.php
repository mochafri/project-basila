<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";
    protected $fillable = [
        "STUDENTID","FULLNAME","MASA_STUDI","PASS_CREDIT","GPA","STUDYPROGRAMID","FACULTYID"
    ];

    /**
     * Accessor untuk predikat - dihitung otomatis dari GPA
     */
    public function getPredikatAttribute()
    {
        return $this->hitungPredikat($this->GPA);
    }

    /**
     * Accessor untuk status - dihitung otomatis dari MASA_STUDI, PASS_CREDIT, dan GPA
     */
    public function getStatusAttribute()
    {
        // Extract numeric value from "10 Semester" format
        $studyPeriod = 0;
        if (preg_match('/(\d+)/', $this->MASA_STUDI, $matches)) {
            $studyPeriod = (int)$matches[1];
        }
        
        return $this->hitungStatus($studyPeriod, $this->PASS_CREDIT, $this->GPA, $this->STUDYPROGRAMID);
    }

    /**
     * Hitung predikat berdasarkan IPK
     */
    public function hitungPredikat($gpa)
    {
        if ($gpa == 4.00) {
            return 'Sempurna (Summa Cumlaude)';
        } elseif ($gpa >= 3.51 && $gpa < 4.00) {
            return 'Dengan Pujian (Cumlaude)';
        } elseif ($gpa >= 3.00) {
            return 'Sangat Memuaskan (Very Good)';
        } elseif ($gpa >= 2.75) {
            return 'Memuaskan (Good)';
        } else {
            return 'Tanpa Predikat (No Predicate)';
        }
    }

    /**
     * Deteksi jenjang pendidikan dari STUDYPROGRAMID
     * Berdasarkan mapping dari API Telkom University
     */
    private function getJenjangFromProdi($prodiId)
    {
        // Mapping STUDYPROGRAMID ke Jenjang
        $prodiMapping = [
            // D3
            51 => 'D3', 32 => 'D3', 73 => 'D3', 54 => 'D3', 72 => 'D3', 
            14 => 'D3', 71 => 'D3',
            
            // D4
            124 => 'D4', 33 => 'D4',
            
            // S1
            94 => 'S1', 93 => 'S1', 95 => 'S1', 91 => 'S1', 92 => 'S1', 
            98 => 'S1', 97 => 'S1', 29 => 'S1', 101 => 'S1', 11 => 'S1', 
            12 => 'S1', 13 => 'S1', 62 => 'S1', 22 => 'S1', 60 => 'S1', 
            21 => 'S1', 139 => 'S1', 89 => 'S1', 31 => 'S1', 61 => 'S1', 
            30 => 'S1', 38 => 'S1', 46 => 'S1', 41 => 'S1', 58 => 'S1', 
            103 => 'S1', 42 => 'S1', 44 => 'S1', 48 => 'S1', 102 => 'S1', 
            43 => 'S1', 47 => 'S1', 141 => 'S1',
            
            // S2
            143 => 'S2', 57 => 'S2', 78 => 'S2', 10 => 'S2', 80 => 'S2', 
            49 => 'S2', 79 => 'S2', 110 => 'S2', 108 => 'S2', 85 => 'S2', 
            99 => 'S2',
            
            // S3
            111 => 'S3', 52 => 'S3', 112 => 'S3',
        ];
        
        return $prodiMapping[$prodiId] ?? 'S1';
    }


     
    public function hitungStatus($studyPeriod, $sks, $ipk, $prodiId = null)
    {

        if ($prodiId === null) {

            if ($studyPeriod <= 8 && $sks >= 110 && $ipk >= 3.01) {
                return 'Eligible';
            }
            return 'Tidak Eligible';
        }
        
        
        $jenjang = $this->getJenjangFromProdi($prodiId);
        

        switch ($jenjang) {
            case 'D3':

                if ($studyPeriod <= 10 && $sks >= 110 && $ipk >= 2.75) {
                    return 'Eligible';
                }
                break;
                
            case 'D4':

                if ($studyPeriod <= 14 && $sks >= 144 && $ipk >= 2.75) {
                    return 'Eligible';
                }
                break;
                
            case 'S1':

                if ($studyPeriod <= 14 && $sks >= 144 && $ipk >= 2.75) {
                    return 'Eligible';
                }
                break;
                
            case 'S2':

                if ($studyPeriod <= 8 && $sks >= 36 && $ipk >= 3.00) {
                    return 'Eligible';
                }
                break;
                
            case 'S3':

                if ($studyPeriod <= 10 && $sks >= 40 && $ipk >= 3.00) {
                    return 'Eligible';
                }
                break;
        }
        
        return 'Tidak Eligible';
    }
}
