<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'preferred_lang' => 'required|in:tk,ru,en',
        ]);

        $user->update($request->only('full_name', 'username', 'email', 'preferred_lang'));

        auth()->user()->logAction("Updated personal profile info");

        return back()->with('success', __('Profile updated successfully'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('Current password does not match')]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        auth()->user()->logAction("Changed account password");

        return back()->with('success', __('Password changed successfully'));
    }
}
