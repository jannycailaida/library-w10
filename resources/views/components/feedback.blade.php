@php
    $status = session('status');
    $status = match ($status) {
        'profile-updated' => 'Your profile details have been saved.',
        'password-updated' => 'Your password has been updated.',
        'verification-link-sent' => 'A new verification link has been sent to your email address.',
        default => $status,
    };
@endphp
@if ($status)
    <div class="alert-success" role="status">{{ $status }}</div>
@endif
@if ($errors->any())
    <div class="alert-error" role="alert">
        <p class="font-semibold">Please check the following:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif
