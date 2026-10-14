<x-guest-layout>
    {{-- Compact Header with Integrated Logo & Tagline --}}
    <div class="text-center mb-4">
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold mb-1.5">
            <span>📚</span>
            <span>LibraryHub Portal</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            Welcome back!
        </h2>
        <p class="text-xs font-medium text-slate-500 mt-0.5">
            Your next book is waiting.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-3">
        @csrf

        <!-- Email Address -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400 text-xs">
                    📧
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="student@library.test"
                       class="w-full ps-9 pe-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400 text-xs">
                    🔒
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="Password"
                       class="w-full ps-9 pe-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password in 1 compact line -->
        <div class="flex items-center justify-between text-xs pt-0.5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer text-slate-600">
                <input id="remember_me" type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="ms-1.5 text-[11px] font-medium">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition" href="{{ route('password.request') }}">
                    Forgot?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/25 transition transform active:scale-95 flex items-center justify-center space-x-1.5">
            <span>Sign In to LibraryHub</span>
            <span>→</span>
        </button>
    </form>

    {{-- Compact Quick Fill Accounts in 1 row --}}
    <div class="mt-4 pt-3 border-t border-slate-100">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center mb-1.5">
            Quick Fill (Password: <code class="text-indigo-600">password</code>)
        </div>
        <div class="grid grid-cols-3 gap-1.5">
            <button type="button"
                    onclick="document.getElementById('email').value='admin@library.test'; document.getElementById('password').value='password';"
                    class="py-1.5 px-2 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-[11px] font-bold border border-purple-200 transition text-center flex items-center justify-center space-x-1">
                <span>👑</span>
                <span>Admin</span>
            </button>

            <button type="button"
                    onclick="document.getElementById('email').value='librarian@library.test'; document.getElementById('password').value='password';"
                    class="py-1.5 px-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold border border-blue-200 transition text-center flex items-center justify-center space-x-1">
                <span>📚</span>
                <span>Staff</span>
            </button>

            <button type="button"
                    onclick="document.getElementById('email').value='member@library.test'; document.getElementById('password').value='password';"
                    class="py-1.5 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold border border-emerald-200 transition text-center flex items-center justify-center space-x-1">
                <span>🎓</span>
                <span>Student</span>
            </button>
        </div>
    </div>

    {{-- Bottom row: Live indicator + Register link --}}
    <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
        <span class="inline-flex items-center space-x-1 text-emerald-700 font-semibold">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>26 books available now</span>
        </span>
        <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition">
            Register as Student →
        </a>
    </div>
</x-guest-layout>
