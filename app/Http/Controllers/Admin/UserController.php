<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->orderBy('full_name')->paginate(15);
        $departments = Department::all();
        return view('admin.users.index', compact('users', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'role_level' => 'required|in:admin,manager,employee',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'role_level' => $request->role_level,
            'preferred_lang' => $request->preferred_lang ?? 'tk',
            'is_active' => $request->has('is_active'),
        ]);

        auth()->user()->logAction("Created user: " . $user->username);
        return back()->with('success', __('User added successfully'));
    }

    public function edit(User $user)
    {
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'required|exists:departments,id',
            'role_level' => 'required|in:admin,manager,employee',
        ]);

        $user->full_name = $request->full_name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->department_id = $request->department_id;
        $user->role_level = $request->role_level;
        $user->is_active = $request->has('is_active');

        // Only update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        auth()->user()->logAction("Updated user ID: " . $user->id);
        return redirect()->route('admin.users.index')->with('success', __('User updated successfully'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself!');
        }

        auth()->user()->logAction("Deleted user: " . $user->username);
        $user->delete();

        return back()->with('success', __('User deleted successfully'));
    }
}
