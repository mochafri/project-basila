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
        'no_sk',
        'fakultas_id',
        'prodi_id',
        'periode',
        'approval_status',
        'catatan',
        'approved_at',
        'created_by',
    ];
    
}
