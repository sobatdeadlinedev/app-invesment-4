<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = User::role('member')
            ->with('roles')
            ->latest()
            ->get();

        return view('admin.pages.user.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'level' => 'nullable|integer|min:1|max:100',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'level' => $request->level,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User updated successfully');
    }
}
