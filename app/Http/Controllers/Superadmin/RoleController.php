<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($perm) {
            $parts = explode('.', $perm->name);
            return $parts[0] ?? 'lainnya';
        });

        return view('superadmin.roles.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $newPermissionIds = $validated['permissions'] ?? [];
        $oldPermissionNames = $role->permissions->pluck('name')->toArray();

        $role->permissions()->sync($newPermissionIds);
        $role->load('permissions');
        $newPermissionNames = $role->permissions->pluck('name')->toArray();

        // RULES.md Section 4.C.2: Wajib mencatat audit log perubahan role/permission
        AuditLog::record(
            action: 'role.kelola',
            targetType: 'Role',
            targetId: $role->id,
            oldValues: ['role' => $role->name, 'permissions' => $oldPermissionNames],
            newValues: ['role' => $role->name, 'permissions' => $newPermissionNames],
            actorId: Auth::id()
        );

        return back()->with('success', "Matriks izin untuk peran '{$role->display_name}' berhasil diperbarui.");
    }
}

