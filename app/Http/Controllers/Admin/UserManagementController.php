<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // 1. Show the list of users
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    // 2. Show the create form
    public function create()
    {
        return view('admin.users.create');
    }

    // 3. Store the new user in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,super_admin',
            'status'   => 'required|in:active,inactive',
        ]);

        User::create($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    // 4. Show the edit form
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // 5. Update the user details
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,super_admin',
            'status'   => 'required|in:active,inactive',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User details updated successfully.');
    }

    // 6. Reset Password specifically
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => $request->password
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Password reset successfully for ' . $user->name);
    }

    // 7. Delete the user
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}