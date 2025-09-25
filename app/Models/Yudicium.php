<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yudicium extends Model
{
    use HasFactory;
    protected $table = 'yudiciums';
    protected $fillable = [
        'no_yudicium',
        'fakultas',
        'prodi',
        'periode',
        'total_mahasiswa',
    ];
}
