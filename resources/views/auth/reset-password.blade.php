<x-guest-layout>
    <x-slot name="title">Choose a new password</x-slot>
    <header class="mb-6"><h1 class="font-serif text-3xl tracking-tight">Choose a new password</h1><p class="mt-2 text-sm leading-6 text-stone-600">Use at least 8 characters and confirm your new password below.</p></header>
    <x-feedback />
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div><x-input-label for="email" value="Email address" /><x-text-input id="email" name="email" type="email" :value="old('email', $request->email)" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" id="email-error" class="mt-1.5" /></div>
        <div><x-input-label for="password" value="New password" /><x-password-input autocomplete="new-password" minlength="8" /></div>
        <div><x-input-label for="password_confirmation" value="Confirm new password" /><x-password-input name="password_confirmation" autocomplete="new-password" minlength="8" /></div>
        <button type="submit" class="btn-primary w-full">Reset password</button>
    </form>
    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="text-link">Back to sign in</a></p>
</x-guest-layout>
