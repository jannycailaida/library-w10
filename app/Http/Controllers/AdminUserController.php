<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::in(['all', 'member', 'librarian', 'admin'])],
        ]);
        $search = trim($request->string('search')->toString());
        $role = $request->input('role') ?: 'all';
        $users = User::with('member')
            ->when($search !== '', fn (Builder $users): Builder => $users->where(fn (Builder $names): Builder => $names->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->when($role !== 'all', fn (Builder $users): Builder => $users->where('role', $role))
            ->orderBy('name')->orderBy('id')->paginate(15)->withQueryString();

        return view('admin.users', compact('users', 'search', 'role'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('updateRole', $user);
        $validated = $request->validate(['role' => ['required', Rule::in(['member', 'librarian', 'admin'])]]);
        $user->role = $validated['role'];
        $user->save();

        return back()->with('status', "Role for {$user->name} updated to {$user->role}.");
    }
}
