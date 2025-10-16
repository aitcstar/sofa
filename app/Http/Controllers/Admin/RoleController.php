<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount('employees', 'permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'employees');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion if role has employees
        if ($role->employees()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدور لأنه مرتبط بموظفين');
        }

        // Prevent deletion of super_admin role
        if ($role->name === 'super_admin') {
            return back()->with('error', 'لا يمكن حذف دور مدير النظام');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }

    /**
     * Toggle role active status.
     */
    public function toggleStatus(Role $role)
    {
        $role->update(['is_active' => !$role->is_active]);

        return back()->with('success', 'تم تحديث حالة الدور بنجاح');
    }

    /**
     * Duplicate a role.
     */
    public function duplicate(Role $role)
    {
        $newRole = $role->replicate();
        $newRole->name = $role->name . '_copy';
        $newRole->display_name = $role->display_name . ' (نسخة)';
        $newRole->save();

        // Copy permissions
        $newRole->permissions()->sync($role->permissions->pluck('id'));

        return redirect()->route('admin.roles.edit', $newRole)
            ->with('success', 'تم نسخ الدور بنجاح');
    }
}

