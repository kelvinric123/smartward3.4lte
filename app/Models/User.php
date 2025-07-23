<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'active',
        'last_login_at',
        'hospital_id',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'active' => 'boolean',
        'hospital_id' => 'integer',
    ];

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        return !! $role->intersect($this->roles)->count();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get beds assigned to this nurse
     */
    public function beds()
    {
        return $this->hasMany(Bed::class, 'nurse_id');
    }
    
    /**
     * Get the hospital this user belongs to
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the admin panel image for this user.
     */
    public function adminlte_image()
    {
        // Return a data URI for a simple default avatar to avoid 404 errors
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg width="128" height="128" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
                <circle cx="64" cy="64" r="64" fill="#e9ecef"/>
                <circle cx="64" cy="50" r="20" fill="#6c757d"/>
                <circle cx="64" cy="100" r="30" fill="#6c757d"/>
            </svg>
        ');
    }

    /**
     * Get the admin panel description for this user.
     */
    public function adminlte_desc()
    {
        // Get user roles as description
        $roles = $this->roles->pluck('name')->toArray();
        return !empty($roles) ? implode(', ', $roles) : ($this->email ?? 'User');
    }

    /**
     * Get the admin panel profile URL for this user.
     */
    public function adminlte_profile_url()
    {
        return '#'; // You can customize this to point to a profile page
    }
}
