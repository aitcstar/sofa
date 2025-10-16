<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $guard = 'employee';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'job_title',
        'role_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the role of the employee.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get assigned orders for the employee.
     */
    public function assignedOrders()
{
    return $this->hasMany(OrderAssignment::class, 'user_id');
}


    /**
     * Get lead activities by the employee.
     */
    public function leadActivities()
    {
        return $this->hasMany(LeadActivity::class);
    }

    /**
     * Check if employee has a specific permission.
     */
    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionName);
    }

    /**
     * Check if employee has any of the given permissions.
     */
    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if employee has all of the given permissions.
     */
    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for the employee through their role.
     */
    public function getPermissions()
    {
        if (!$this->role) {
            return collect();
        }

        return $this->role->permissions;
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
