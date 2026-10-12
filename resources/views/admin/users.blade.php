<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    ⚙️ User & Role Management
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Admin panel: promote, assign roles, and manage system access.</p>
            </div>
            <span class="px-3 py-1 bg-purple-100 text-purple-700 font-bold text-xs rounded-full">
                Admin Area
            </span>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash message / Errors --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center space-x-2 shadow-sm">
                <span class="text-emerald-500 text-lg">✓</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Notice Box --}}
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center space-x-3">
            <span class="text-lg">🛡️</span>
            <div>
                <span class="font-bold">Security Policy:</span>
                Administrators can promote or reassign user roles. An admin is prevented by authorization policy from demoting their own account to maintain access integrity.
            </div>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold">User</th>
                            <th class="px-6 py-4 font-semibold">Email</th>
                            <th class="px-6 py-4 font-semibold">Current Role</th>
                            <th class="px-6 py-4 font-semibold">Member Profile</th>
                            <th class="px-6 py-4 font-semibold text-right">Change Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach ($users as $user)
                            @php
                                $isSelf = Auth::id() === $user->id;
                            @endphp
                            <tr class="hover:bg-gray-50 transition {{ $isSelf ? 'bg-indigo-50/40' : '' }}">
                                {{-- Name --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 flex items-center space-x-2">
                                        <span>{{ $user->name }}</span>
                                        @if ($isSelf)
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">You</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">ID: #{{ $user->id }}</div>
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">
                                    {{ $user->email }}
                                </td>

                                {{-- Role badge --}}
                                <td class="px-6 py-4">
                                    @if ($user->isAdmin())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                                            👑 Admin
                                        </span>
                                    @elseif ($user->isLibrarian())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                            📚 Librarian
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                            👤 Member
                                        </span>
                                    @endif
                                </td>

                                {{-- Linked Member --}}
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    @if ($user->member)
                                        <span class="text-emerald-700 font-medium">✓ Card #{{ $user->member->id }}</span>
                                    @else
                                        <span class="text-gray-400">No card</span>
                                    @endif
                                </td>

                                {{-- Action Form --}}
                                <td class="px-6 py-4 text-right">
                                    @if ($isSelf)
                                        <span class="text-xs text-gray-400 italic">Cannot edit own role</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="inline-flex items-center gap-2 justify-end">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="text-xs py-1 px-2.5 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                                                <option value="member" @selected($user->role === 'member')>Member</option>
                                                <option value="librarian" @selected($user->role === 'librarian')>Librarian</option>
                                                <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                            </select>
                                            <button type="submit" class="px-3 py-1 bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                                Update
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

