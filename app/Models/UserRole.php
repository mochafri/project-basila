<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';
    protected $primaryKey = 'user_nib';
    public $incrementing = true;

    protected $fillable = [
        'user_nib',
        'user_fullname',
        'role_id',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_nib', 'id');
    }

    /**
     * Get role name
     */
    public function getRoleName()
    {
        $roles = [
            10000 => 'Administrator',
            50055 => 'Kamar Begian',
            34803 => 'Kaur Akademik 1',
            30057 => 'Kaur Akademik 2',
            30056 => 'Staff A1',
            30060 => 'Staff A2',
            30055 => 'Staff B1',
            30056 => 'Staff B2',
        ];

        return $roles[$this->role_id] ?? 'Unknown';
    }

    /**
     * Scope untuk filter by role
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope untuk administrator
     */
    public function scopeAdministrator($query)
    {
        return $query->where('role_id', 10000);
    }
}
