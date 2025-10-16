<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'module',
        'description',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Get permissions by module.
     */
    public static function getByModule($module)
    {
        return static::where('module', $module)->get();
    }

    /**
     * Get all modules.
     */
    public static function getModules()
    {
        return static::distinct()->pluck('module');
    }
}

