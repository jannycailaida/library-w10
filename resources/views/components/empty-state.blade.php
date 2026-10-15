@props(['title', 'description', 'href' => null, 'action' => null])
<div {{ $attributes->class(['empty-state']) }}>
    <svg class="mx-auto mb-4 h-9 w-9 text-brand-700" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.5C9 4.5 5.5 4.5 2 6v14c3.5-1.5 7-1.5 10 .5m0-14c3-2 6.5-2 10-.5v14c-3.5-1.5-7-1.5-10 .5m0-14v14"/></svg>
    <h2 class="section-title">{{ $title }}</h2>
    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-600">{{ $description }}</p>
    @if ($href && $action)<a class="btn-primary mt-5" href="{{ $href }}">{{ $action }}</a>@endif
    {{ $slot }}
</div>
