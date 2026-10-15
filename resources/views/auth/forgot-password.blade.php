<x-guest-layout>
    <x-slot name="title">Reset your password</x-slot>
    <header class="mb-6"><h1 class="font-serif text-3xl tracking-tight">Forgot your password?</h1><p class="mt-2 text-sm leading-6 text-stone-600">Enter your account email and we’ll send you a password reset link.</p></header>
    <x-feedback />
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div><x-input-label for="email" value="Email address" /><x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" id="email-error" class="mt-1.5" /></div>
        <button type="submit" class="btn-primary w-full">Send reset link</button>
    </form>
    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="text-link">Back to sign in</a></p>
</x-guest-layout>
