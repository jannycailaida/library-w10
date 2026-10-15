<x-app-layout>
    <x-slot name="title">Users and roles</x-slot>
    <div class="page-shell">
        <x-page-heading title="Users and roles" description="Find library accounts and give each person the access they need." />
        <x-feedback />
        <details class="mb-6 rounded-lg border border-stone-200 bg-white p-4">
            <summary class="cursor-pointer text-sm font-semibold text-stone-800">What each role can do</summary>
            <dl class="mt-4 grid gap-4 text-sm leading-6 sm:grid-cols-3">
                <div><dt class="font-semibold">Member</dt><dd class="text-stone-600">Browse, borrow available books, and view their own loan history.</dd></div>
                <div><dt class="font-semibold">Librarian</dt><dd class="text-stone-600">Member access, book management, and circulation desk returns.</dd></div>
                <div><dt class="font-semibold">Administrator</dt><dd class="text-stone-600">Librarian access and user role management. Your own role is protected.</dd></div>
            </dl>
        </details>
        <form method="GET" action="{{ route('admin.users.index') }}" class="panel mb-6 grid items-end gap-4 p-4 sm:grid-cols-[minmax(0,1fr)_12rem_auto]">
            <div><label for="user-search" class="field-label">Find a user</label><input type="search" id="user-search" name="search" value="{{ $search }}" maxlength="255" placeholder="Name or email address"></div>
            <div><label for="role-filter" class="field-label">Role</label><select id="role-filter" name="role">@foreach (['all' => 'All roles', 'member' => 'Member', 'librarian' => 'Librarian', 'admin' => 'Administrator'] as $value => $label)<option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>@endforeach</select></div>
            <button class="btn-primary" type="submit">Find users</button>
        </form>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3"><h2 class="section-title">{{ $users->total() }} {{ $users->total() === 1 ? 'account' : 'accounts' }}</h2>@if ($search !== '' || $role !== 'all')<a href="{{ route('admin.users.index') }}" class="btn text-brand-700">Clear filters</a>@endif</div>
        <section class="panel" aria-label="Library user accounts">
            @forelse ($users as $user)
                <article class="record-grid">
                    <div class="min-w-0"><h3 class="font-semibold">{{ $user->name }} @if(auth()->id() === $user->id)<span class="ml-1 text-xs font-normal text-stone-600">(you)</span>@endif</h3><p class="mt-1 break-all text-sm text-stone-600">{{ $user->email }}</p></div>
                    <div><span class="badge-active">{{ $user->isAdmin() ? 'Administrator' : ucfirst($user->role) }}</span></div>
                    <p class="text-sm text-stone-600">{{ $user->member ? 'Library card #'.$user->member->id : 'No library card' }}</p>
                    @if (auth()->id() === $user->id)
                        <p class="text-sm text-stone-600">Your role is protected.</p>
                    @else
                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="flex flex-wrap items-end gap-2 lg:flex-nowrap" data-confirm="Change the library access role for {{ $user->name }}? Please verify the selected role before confirming.">
                            @csrf @method('PATCH')
                            <div class="min-w-0 flex-1"><label for="role-{{ $user->id }}" class="sr-only">Role for {{ $user->name }}</label><select id="role-{{ $user->id }}" name="role" class="min-w-[9rem]"><option value="member" @selected($user->role === 'member')>Member</option><option value="librarian" @selected($user->role === 'librarian')>Librarian</option><option value="admin" @selected($user->role === 'admin')>Administrator</option></select></div>
                            <button type="submit" class="btn-secondary shrink-0" aria-label="Save role for {{ $user->name }}">Save role</button>
                        </form>
                    @endif
                </article>
            @empty
                <x-empty-state title="No users found" description="Try another name or email, or select a different role." :href="route('admin.users.index')" action="View all users" />
            @endforelse
        </section>
        <div class="mt-6">{{ $users->links() }}</div>
    </div>
</x-app-layout>
