<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminUserController extends Controller
{
    /**
     * /admin/users - list users with their roles (admins only).
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $users = User::with('member')->orderBy('role')->orderBy('name')->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Update a user's role. Admin cannot demote/change themselves.
     */
    public function updateRole(Request $request, User $user)
    {
        Gate::authorize('updateRole', $user);

        $validated = $request->validate([
            'role' => ['required', 'in:member,librarian,admin'],
        ]);

        $user->role = $validated['role'];
        $user->save();

        return back()->with('status', "Role for {$user->name} successfully updated to {$user->role}.");
    }
}
