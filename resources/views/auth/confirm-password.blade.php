<x-guest-layout>
    <x-slot name="title">Confirm your password</x-slot>
    <header class="mb-6"><h1 class="font-serif text-3xl tracking-tight">Confirm your password</h1><p class="mt-2 text-sm leading-6 text-stone-600">Enter your current password to continue to this secure area.</p></header>
    <x-feedback />
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <div><x-input-label for="password" value="Current password" /><x-password-input autofocus /></div>
        <button type="submit" class="btn-primary w-full">Confirm password</button>
    </form>
    <p class="mt-6 text-center text-sm"><a href="{{ route('dashboard') }}" class="text-link">Back to overview</a></p>
</x-guest-layout>
