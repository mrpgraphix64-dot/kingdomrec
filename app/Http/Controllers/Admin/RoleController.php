<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($id);
        
        // Prevent editing Master Admin name if desired, or let it be
        if ($role->name === 'Master Admin' && $request->name !== 'Master Admin') {
            return back()->with('error', 'Cannot rename the Master Admin role.');
        }

        $role->name = $request->name;
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            // Optional: prevent clearing Master Admin's permissions accidentally
            if ($role->name === 'Master Admin') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions([]);
            }
        }

        return back()->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->name === 'Master Admin') {
            return back()->with('error', 'Cannot delete the Master Admin role.');
        }
        
        $role->delete();
        return back()->with('success', 'Role deleted successfully.');
    }
}
