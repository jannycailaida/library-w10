<section class="panel p-5 sm:p-7" aria-labelledby="delete-account-title">
    <h2 id="delete-account-title" class="section-title">Delete account</h2>
    <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">Permanently remove your sign-in account. Return any borrowed books first. Existing circulation records are retained.</p>
    <details class="mt-4" @if($errors->userDeletion->isNotEmpty()) open @endif>
        <summary class="inline-flex min-h-[2.75rem] cursor-pointer items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-800">Delete my account</summary>
        <form method="POST" action="{{ route('profile.destroy') }}" class="mt-5 max-w-md space-y-4" data-confirm="Permanently delete your LibraryHub account? You will be signed out, and this action cannot be undone.">
            @csrf @method('DELETE')
            <div><x-input-label for="delete_password" value="Confirm with your current password" /><x-password-input name="password" id="delete_password" error-bag="userDeletion" /></div>
            <button class="btn-danger" type="submit">Permanently delete account</button>
        </form>
    </details>
</section>
