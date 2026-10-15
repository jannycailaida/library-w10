<section class="panel grid gap-6 p-5 sm:p-7 md:grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)]" aria-labelledby="profile-information">
    <header><h2 id="profile-information" class="section-title">Profile information</h2><p class="mt-2 text-sm leading-6 text-stone-600">Your name and email are also kept in sync with your library card.</p></header>
    <div>
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">@csrf<p class="mb-2 text-sm text-amber-900">Your email address is not verified.</p><button class="btn-secondary" type="submit">Resend verification email</button></form>
        @endif
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf @method('PATCH')
            <div><x-input-label for="name" value="Full name" /><x-text-input id="name" name="name" :value="old('name', $user->name)" required autocomplete="name" maxlength="191" /><x-input-error :messages="$errors->get('name')" id="name-error" class="mt-1.5" /></div>
            <div><x-input-label for="email" value="Email address" /><x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" maxlength="191" /><x-input-error :messages="$errors->get('email')" id="email-error" class="mt-1.5" /></div>
            <button type="submit" class="btn-primary">Save profile</button>
        </form>
    </div>
</section>
