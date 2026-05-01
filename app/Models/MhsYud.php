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
        'mahasiswa_id',
        'yudicium_id',
        'id_smt_masuk',
        'fakultas_id',
        'prody_id',
        'name',
        'tmp_lahir',
        'tgl_lahir',
        'study_period',
        'masa_studi',
        'pass_sks',
        'sks_lulus',
        'ipk',
        'predikat',
        'status'
    ];

    /**
     * Hitung predikat berdasarkan IPK
     */
    public function getPredikat($gpa)
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
     * Relasi ke tabel yudiciums
     */
    public function yudicium()
    {
        return $this->belongsTo(Yudicium::class, 'yudicium_id');
    }

    /**
     * Relasi ke tabel mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'STUDENTID');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope untuk filter berdasarkan status final
     */
    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    /**
     * Scope untuk filter berdasarkan yudicium_id
     */
    public function scopeByYudicium($query, $yudisiumId)
    {
        return $query->where('yudicium_id', $yudisiumId);
    }
}