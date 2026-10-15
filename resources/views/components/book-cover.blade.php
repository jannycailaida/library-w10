@props(['book'])
<div {{ $attributes->class(['flex items-center justify-center overflow-hidden rounded-lg bg-stone-100 p-3']) }}>
    @if ($book->cover_path)
        <img src="{{ asset($book->cover_path) }}" alt="Cover of {{ $book->title }}" class="h-full max-h-full w-full object-contain" loading="lazy" decoding="async" data-book-cover>
    @else
        <div class="flex h-full w-full flex-col justify-between rounded bg-brand-800 p-4 text-brand-50">
            <span class="text-xs font-medium">{{ $book->author?->name }}</span>
            <span class="font-serif text-lg leading-snug">{{ $book->title }}</span>
            <span class="text-xs">{{ $book->published_year }}</span>
        </div>
    @endif
</div>
