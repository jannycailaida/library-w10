<x-guest-layout>
    <x-slot name="title">Create an account</x-slot>
    <header class="mb-5"><h1 class="font-serif text-3xl tracking-tight">Join the library</h1><p class="mt-2 text-sm leading-6 text-stone-600">Create your account and start borrowing from the university collection.</p></header>
    <x-feedback />
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div><x-input-label for="name" value="Full name" /><x-text-input id="name" name="name" :value="old('name')" required autofocus autocomplete="name" maxlength="191" /><x-input-error :messages="$errors->get('name')" id="name-error" class="mt-1.5" /></div>
        <div><x-input-label for="email" value="Email address" /><x-text-input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" maxlength="191" /><x-input-error :messages="$errors->get('email')" id="email-error" class="mt-1.5" /></div>
        <div><x-input-label for="password" value="Password" /><x-password-input autocomplete="new-password" minlength="8" placeholder="At least 8 characters" /></div>
        <div><x-input-label for="password_confirmation" value="Confirm password" /><x-password-input name="password_confirmation" autocomplete="new-password" minlength="8" /></div>
        <button type="submit" class="btn-primary w-full">Create account</button>
    </form>
    <p class="mt-5 border-t border-stone-200 pt-4 text-center text-sm text-stone-600">Already have an account? <a href="{{ route('login') }}" class="text-link">Sign in</a></p>
</x-guest-layout>
