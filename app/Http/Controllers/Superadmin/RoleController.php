<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Cache::remember('superadmin_roles_matrix', 3600, function () {
            return Role::with('permissions')->get();
        });

        $permissions = Cache::remember('superadmin_permissions_grouped', 3600, function () {
            return Permission::all()->groupBy(function ($perm) {
                $parts = explode('.', $perm->name);
                return $parts[0] ?? 'lainnya';
            });
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

        Cache::forget('superadmin_roles_matrix');

        // RULES.md Section 4.C.2: Wajib mencatat audit log perubahan role/permission
        AuditLog::record(
            action: 'role.kelola',
            targetType: 'Role',
            targetId: $role->id,
            oldValues: ['role' => $role->name, 'permissions' => $oldPermissionNames],
            newValues: ['role' => $role->name, 'permissions' => $newPermissionNames],
            actorId: Auth::id()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Matriks izin untuk peran '{$role->display_name}' berhasil disimpan.",
                'permission_count' => $role->permissions->count(),
                'role_id' => $role->id,
            ]);
        }

        return back()->with('success', "Matriks izin untuk peran '{$role->display_name}' berhasil diperbarui.");
    }

    /**
     * Endpoint Toggle Izin Instan (AJAX / UX Ramah Pengguna)
     */
    public function togglePermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_id' => ['required', 'exists:permissions,id'],
            'state' => ['required', 'boolean'],
        ]);

        $permissionId = (int) $validated['permission_id'];
        $state = (bool) $validated['state'];
        $permission = Permission::findOrFail($permissionId);

        $oldPermissionNames = $role->permissions->pluck('name')->toArray();

        if ($state) {
            $role->permissions()->syncWithoutDetaching([$permissionId]);
        } else {
            $role->permissions()->detach($permissionId);
        }

        $role->load('permissions');
        $newPermissionNames = $role->permissions->pluck('name')->toArray();

        Cache::forget('superadmin_roles_matrix');

        AuditLog::record(
            action: 'role.kelola',
            targetType: 'Role',
            targetId: $role->id,
            oldValues: ['role' => $role->name, 'permissions' => $oldPermissionNames],
            newValues: ['role' => $role->name, 'permissions' => $newPermissionNames],
            actorId: Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => "Izin '{$permission->display_name}' " . ($state ? 'diaktifkan' : 'dinonaktifkan') . " untuk peran {$role->display_name}.",
            'permission_count' => $role->permissions->count(),
            'permission_id' => $permissionId,
            'state' => $state,
            'role_id' => $role->id,
        ]);
    }
}
