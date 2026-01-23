<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pejabat extends Model
{
    protected $table = 'pejabats';

    protected $fillable = [
        'fakultas_id',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'jabatan',
        'level',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    protected $appends = [
        'nama_lengkap'
    ];

    public function getNamaLengkapAttribute()
    {
        $nama = trim($this->nama);

        if (!empty(trim($this->gelar_depan ?? ''))) {
            $nama = trim($this->gelar_depan) . ' ' . $nama;
        }

        if (!empty(trim($this->gelar_belakang ?? ''))) {
            $nama .= ', ' . trim($this->gelar_belakang);
        }

        return $nama;
    }


    /* =========================
     |  RELATION
     ========================= */

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    /* =========================
     |  SCOPES
     ========================= */

    /**
     * Hanya pejabat aktif
     */
    public function scopeAktif(Builder $query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Ambil penandatangan berdasarkan fakultas
     * fallback ke Rektor (universitas)
     */
    public function scopePenandatanganYudisium(Builder $query, $fakultasId)
    {
        return $query
            ->where(function ($q) use ($fakultasId) {
                $q->where('fakultas_id', $fakultasId)
                    ->orWhereNull('fakultas_id');
            })
            ->aktif()
            ->orderByRaw('fakultas_id IS NULL');
    }
}
