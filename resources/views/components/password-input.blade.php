@props(['name' => 'password', 'id' => null, 'autocomplete' => 'current-password', 'required' => true, 'errorBag' => 'default'])
@php($inputId = $id ?? $name)
@php($fieldErrors = $errors->getBag($errorBag))
<div x-data="{ visible: false }" class="relative">
    <input id="{{ $inputId }}" name="{{ $name }}" type="password" :type="visible ? 'text' : 'password'"
        autocomplete="{{ $autocomplete }}" @required($required)
        aria-invalid="{{ $fieldErrors->has($name) ? 'true' : 'false' }}"
        @if ($fieldErrors->has($name)) aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes->class(['pr-20']) }}>
    <button type="button" class="absolute inset-y-0 right-0 min-w-[4.5rem] rounded-r-lg px-3 text-sm font-semibold text-brand-700 hover:bg-brand-50"
        @click="visible = !visible" :aria-pressed="visible" :aria-label="visible ? 'Hide password' : 'Show password'" aria-controls="{{ $inputId }}">
        <span x-text="visible ? 'Hide' : 'Show'">Show</span>
    </button>
</div>
<x-input-error :messages="$fieldErrors->get($name)" id="{{ $inputId }}-error" class="mt-1.5" />
