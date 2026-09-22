<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'admin')->with(['roles', 'team']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('role_filter')) {
            $roleName = $request->role_filter;
            $query->whereHas('roles', function($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        $admins = $query->paginate(15, ['*'], 'admins_page')->withQueryString();
        
        // Attach phone from linked Team record
        $admins->each(function ($admin) {
            $admin->phone = $admin->team->phone ?? null;
        });
        
        // Fetch team members with linked user's roles
        $teamMembers = Team::with('user.roles')->orderBy('id', 'desc')->paginate(15, ['*'], 'team_page')->withQueryString();
        $teamMembers->each(function ($member) {
            $member->linkedUser = $member->user;
        });
        
        $roles = Role::all();
        
        return view('admin.admins', compact('admins', 'roles', 'teamMembers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'role_name' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Assign the dynamic Spatie role
        $admin->assignRole($request->role_name);

        // Also create a Team profile for this admin
        Team::create([
            'user_id' => $admin->id,
            'name' => $request->name,
            'role' => $request->job_title ?? $request->role_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Added Admin & Team Member',
            'description' => "Added {$request->name} with role: {$request->role_name}"
        ]);

        return back()->with('success', 'Admin user created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => ['nullable', Password::defaults()],
            'role_name' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $admin = User::findOrFail($id);
        
        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->has('is_active')) {
            $admin->is_active = $request->is_active;
        }
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        
        $admin->save();

        // Prevent stripping the current Master Admin of their role if they are the only one
        $admin->syncRoles([$request->role_name]);

        // Also update linked Team profile
        $team = Team::where('user_id', $admin->id)->first();
        if ($team) {
            $team->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? $team->phone,
                'role' => $request->job_title ?? $team->role,
            ]);
        }

        return back()->with('success', 'Admin user updated successfully.');
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        
        // Prevent deleting oneself
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($admin->hasRole('Master Admin')) {
            return back()->with('error', 'Master Admin users cannot be deleted.');
        }

        // Also delete linked Team profile
        Team::where('user_id', $admin->id)->delete();

        $admin->delete();
        return back()->with('success', 'Admin user deleted successfully.');
    }
}
