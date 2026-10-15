<x-app-layout>
    <x-slot name="title">{{ $book->exists ? 'Edit book' : 'Add a book' }}</x-slot>
    <div class="page-shell max-w-3xl">
        <nav aria-label="Breadcrumb" class="mb-5 text-sm"><a href="{{ route('books.index') }}" class="text-link">← Catalogue</a></nav>
        <x-page-heading :title="$book->exists ? 'Edit book' : 'Add a book'" description="Keep the catalogue accurate so readers can find the right edition." />
        <x-feedback />
        <form method="POST" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" class="panel space-y-5 p-5 sm:p-7">
            @csrf
            @if ($book->exists) @method('PUT') @endif
            <p class="text-sm text-stone-600">All fields are required unless marked optional.</p>
            <div>
                <x-input-label for="title" value="Book title" />
                <x-text-input id="title" name="title" :value="old('title', $book->title)" required maxlength="191" autofocus />
                <x-input-error :messages="$errors->get('title')" id="title-error" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="author_id" value="Author" />
                <select id="author_id" name="author_id" required aria-invalid="{{ $errors->has('author_id') ? 'true' : 'false' }}" @error('author_id') aria-describedby="author_id-error" @enderror>
                    <option value="">Select an author</option>
                    @foreach ($authors as $author)<option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>{{ $author->name }}</option>@endforeach
                </select>
                <x-input-error :messages="$errors->get('author_id')" id="author_id-error" class="mt-1.5" />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="isbn" value="ISBN" />
                    <x-text-input id="isbn" name="isbn" :value="old('isbn', $book->isbn)" required minlength="13" maxlength="13" pattern="[0-9]{13}" inputmode="numeric" aria-describedby="isbn-help isbn-error" />
                    <p class="field-help" id="isbn-help">13 digits, without spaces or hyphens.</p>
                    <x-input-error :messages="$errors->get('isbn')" id="isbn-error" class="mt-1.5" />
                </div>
                <div>
                    <x-input-label for="published_year" value="Publication year" />
                    <x-text-input id="published_year" name="published_year" type="number" :value="old('published_year', $book->published_year)" required min="1500" :max="now()->year + 1" />
                    <x-input-error :messages="$errors->get('published_year')" id="published_year-error" class="mt-1.5" />
                </div>
            </div>
            <div>
                <x-input-label for="cover_path" value="Cover image (optional)" />
                <x-text-input id="cover_path" name="cover_path" :value="old('cover_path', $book->cover_path)" maxlength="191" aria-describedby="cover-help cover_path-error" placeholder="/images/books/cover.jpg" />
                <p class="field-help" id="cover-help">Use a local image path starting with / or a complete http:// or https:// image URL.</p>
                <x-input-error :messages="$errors->get('cover_path')" id="cover_path-error" class="mt-1.5" />
            </div>
            <div class="rounded-lg bg-amber-50 p-4">
                <input type="hidden" name="is_reference" value="0">
                <label for="is_reference" class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" id="is_reference" name="is_reference" value="1" class="mt-1" @checked(old('is_reference', $book->is_reference)) aria-describedby="reference-help is_reference-error">
                    <span><span class="block text-sm font-semibold text-amber-900">Reference material</span><span class="mt-1 block text-sm leading-6 text-amber-900" id="reference-help">For reading inside the library only. Borrowing will be disabled.</span></span>
                </label>
                <x-input-error :messages="$errors->get('is_reference')" id="is_reference-error" class="mt-1.5" />
            </div>
            <div class="flex flex-wrap justify-end gap-3 border-t border-stone-200 pt-5">
                <a href="{{ $book->exists ? route('books.show', $book) : route('books.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">{{ $book->exists ? 'Save changes' : 'Add book' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
