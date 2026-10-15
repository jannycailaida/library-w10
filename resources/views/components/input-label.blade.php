@props(['value' => null])
<label {{ $attributes->class(['field-label']) }}>{{ $value ?? $slot }}</label>
