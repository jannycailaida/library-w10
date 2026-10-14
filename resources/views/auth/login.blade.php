<x-guest-layout>
    {{-- Form Header --}}
    <div class="mb-5">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">
            Welcome back!
        </h2>
        <p class="text-xs font-medium text-slate-500 mt-1">
            Sign in with your library credentials to continue.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="student@library.test"
                       class="w-full ps-10 pe-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full ps-10 pe-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-0.5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs font-medium text-slate-600">Keep me signed in</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button type="submit"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md shadow-indigo-600/25 transition transform active:scale-95 flex items-center justify-center space-x-2">
                <span>Sign In to LibraryHub</span>
                <span>→</span>
            </button>
        </div>
    </form>

    {{-- ⚡ Quick Demo Accounts Auto-Filler --}}
    <div class="mt-5 pt-4 border-t border-slate-100">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-2">
            Quick Fill Demo Accounts (Password: <code class="text-indigo-600 font-mono">password</code>)
        </div>
        <div class="grid grid-cols-3 gap-2">
            <button type="button"
                    onclick="document.getElementById('email').value='admin@library.test'; document.getElementById('password').value='password';"
                    class="p-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200 transition text-center flex flex-col items-center">
                <span class="text-sm">👑</span>
                <span>Admin</span>
            </button>

            <button type="button"
                    onclick="document.getElementById('email').value='librarian@library.test'; document.getElementById('password').value='password';"
                    class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition text-center flex flex-col items-center">
                <span class="text-sm">📚</span>
                <span>Librarian</span>
            </button>

            <button type="button"
                    onclick="document.getElementById('email').value='member@library.test'; document.getElementById('password').value='password';"
                    class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition text-center flex flex-col items-center">
                <span class="text-sm">🎓</span>
                <span>Student</span>
            </button>
        </div>
    </div>

    {{-- Register link footer --}}
    <div class="mt-4 pt-3.5 border-t border-slate-100 text-center text-xs text-slate-500">
        Don't have a library account?
        <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition ms-1">
            Register as a Student →
        </a>
    </div>
</x-guest-layout>
