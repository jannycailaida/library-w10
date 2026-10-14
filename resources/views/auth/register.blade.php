<x-guest-layout>
    {{-- Compact Header with Integrated Logo --}}
    <div class="text-center mb-3.5">
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold mb-1.5">
            <span>📚</span>
            <span>Student Registration</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            Create Student Account
        </h2>
        <p class="text-xs font-medium text-slate-500 mt-0.5">
            Get your instant digital library card to borrow textbooks.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3">
        @csrf

        <!-- Full Name -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400 text-xs">
                    👤
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                       placeholder="Full Name (e.g. Juan Dela Cruz)"
                       class="w-full ps-9 pe-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400 text-xs">
                    📧
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                       placeholder="Student Email (student@university.edu)"
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
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       placeholder="Password (min 8 chars)"
                       class="w-full ps-9 pe-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400 text-xs">
                    🛡️
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="Confirm Password"
                       class="w-full ps-9 pe-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-slate-400 font-medium">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/25 transition transform active:scale-95 flex items-center justify-center space-x-1.5">
            <span>Complete Registration</span>
            <span>→</span>
        </button>
    </form>

    {{-- Bottom row: Live indicator + Sign in link --}}
    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
        <span class="inline-flex items-center space-x-1 text-emerald-700 font-semibold">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>26 books available now</span>
        </span>
        <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition">
            Sign in here →
        </a>
    </div>
</x-guest-layout>
