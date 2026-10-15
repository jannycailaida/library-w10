<x-guest-layout>
    <x-slot name="title">Verify your email</x-slot>
    <header class="mb-6"><h1 class="font-serif text-3xl tracking-tight">Check your email</h1><p class="mt-2 text-sm leading-6 text-stone-600">Open the verification link sent to your email address to verify your library account.</p></header>
    <x-feedback />
    <form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit" class="btn-primary w-full">Resend verification email</button></form>
    <div class="mt-5 flex flex-wrap items-center justify-between gap-3"><a href="{{ route('profile.edit') }}" class="text-link text-sm">Update email address</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn text-stone-600">Sign out</button></form></div>
</x-guest-layout>
