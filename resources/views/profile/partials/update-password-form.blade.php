<section class="panel grid gap-6 p-5 sm:p-7 md:grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)]" aria-labelledby="update-password">
    <header><h2 id="update-password" class="section-title">Change password</h2><p class="mt-2 text-sm leading-6 text-stone-600">Choose a password with at least 8 characters that you do not use elsewhere.</p></header>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf @method('PUT')
        <div><x-input-label for="current_password" value="Current password" /><x-password-input name="current_password" error-bag="updatePassword" /></div>
        <div><x-input-label for="new_password" value="New password" /><x-password-input name="password" id="new_password" autocomplete="new-password" error-bag="updatePassword" minlength="8" /></div>
        <div><x-input-label for="new_password_confirmation" value="Confirm new password" /><x-password-input name="password_confirmation" id="new_password_confirmation" autocomplete="new-password" error-bag="updatePassword" minlength="8" /></div>
        <div><button type="submit" class="btn-primary">Update password</button></div>
    </form>
</section>
