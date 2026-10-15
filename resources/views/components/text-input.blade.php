@props(['disabled' => false])
@php
    $fieldName = $attributes->get('name');
    $hasError = $fieldName && $errors->has($fieldName);
    $description = $attributes->get('aria-describedby', $hasError ? $attributes->get('id', $fieldName).'-error' : null);
@endphp
<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full', 'aria-invalid' => $hasError ? 'true' : 'false', 'aria-describedby' => $description]) }}>
