<x-app-layout>
    <x-slot name="title">My profile</x-slot>
    <div class="page-shell max-w-5xl">
        <x-page-heading title="My profile" description="Keep your library account details up to date and manage your password." />
        <x-feedback />
        <div class="space-y-6">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
