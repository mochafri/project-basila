<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'nip',
    ];

    /**
     * Relasi ke UserRole
     */
    public function userRole()
    {
        return $this->hasOne(UserRole::class, 'user_nib', 'id');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($roleId)
    {
        return $this->userRole && $this->userRole->role_id == $roleId;
    }

    /**
     * Check if user is administrator
     */
    public function isAdministrator()
    {
        return $this->hasRole(10000);
    }

    /**
     * Get user's role name
     */
    public function getRoleName()
    {
        return $this->userRole ? $this->userRole->getRoleName() : 'No Role';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
