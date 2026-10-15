@props(['title', 'description' => ''])
<header {{ $attributes->class(['page-heading']) }}>
    <div class="min-w-0">
        <h1 class="page-title">{{ $title }}</h1>
        @if ($description)<p class="page-description">{{ $description }}</p>@endif
    </div>
    @if ($slot->isNotEmpty())<div class="flex shrink-0 flex-wrap items-center gap-3">{{ $slot }}</div>@endif
</header>
