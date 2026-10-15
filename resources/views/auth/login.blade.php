<x-guest-layout>
    <x-slot name="title">Sign in</x-slot>
    <header class="mb-6"><h1 class="font-serif text-3xl tracking-tight">Welcome back</h1><p class="mt-2 text-sm leading-6 text-stone-600">Sign in to borrow books and manage your library account.</p></header>
    <x-feedback />
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div><x-input-label for="email" value="Email address" /><x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" id="email-error" class="mt-1.5" /></div>
        <div>
            <div class="flex items-center justify-between gap-2"><x-input-label for="password" value="Password" /><a href="{{ route('password.request') }}" class="py-2 text-sm font-semibold text-brand-700 hover:underline">Forgot password?</a></div>
            <x-password-input />
        </div>
        <label for="remember_me" class="flex min-h-[2.75rem] cursor-pointer items-center gap-2.5 text-sm text-stone-700"><input type="checkbox" id="remember_me" name="remember" @checked(old('remember'))>Keep me signed in</label>
        <button type="submit" class="btn-primary w-full">Sign in</button>
    </form>
    <p class="mt-6 border-t border-stone-200 pt-5 text-center text-sm text-stone-600">New to LibraryHub? <a href="{{ route('register') }}" class="text-link">Create an account</a></p>
</x-guest-layout>
