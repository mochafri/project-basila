<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempStatus extends Model
{
    use HasFactory ;
    public $timestamps = false;
    protected $table = 'temp_status';
    protected $fillable = ['status', 'alasan', 'nim'];
}
