<x-app-layout>
    <x-slot name="title">{{ $book->exists ? 'Edit book' : 'Add a book' }}</x-slot>
    <div class="page-shell max-w-3xl">
        <nav aria-label="Breadcrumb" class="mb-5 text-sm"><a href="{{ route('books.index') }}" class="text-link">← Catalogue</a></nav>
        <x-page-heading :title="$book->exists ? 'Edit book' : 'Add a book'" description="Keep the catalogue accurate so readers can find the right edition." />
        <x-feedback />
        <form method="POST" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" enctype="multipart/form-data" class="panel space-y-5 p-5 sm:p-7">
            @csrf
            @if ($book->exists) @method('PUT') @endif
            <p class="text-sm text-stone-600">All fields are required unless marked optional.</p>
            <div>
                <x-input-label for="title" value="Book title" />
                <x-text-input id="title" name="title" :value="old('title', $book->title)" required maxlength="191" autofocus />
                <x-input-error :messages="$errors->get('title')" id="title-error" class="mt-1.5" />
            </div>
            <div data-author-picker>
                <x-input-label for="author_id" value="Author" />
                <select id="author_id" name="author_id" required aria-invalid="{{ $errors->has('author_id') ? 'true' : 'false' }}" aria-describedby="author-help{{ $errors->has('author_id') ? ' author_id-error' : '' }}">
                    <option value="">Select an author</option>
                    @foreach ($authors as $author)<option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>{{ $author->name }}</option>@endforeach
                    <option value="new" @selected(old('author_id') === 'new')>+ Add a new author</option>
                </select>
                <p class="field-help" id="author-help">Author not listed? Choose “Add a new author” to enter their name.</p>
                <x-input-error :messages="$errors->get('author_id')" id="author_id-error" class="mt-1.5" />
                <div class="mt-3" data-new-author>
                    <x-input-label for="new_author_name" value="New author name" />
                    <x-text-input id="new_author_name" name="new_author_name" :value="is_string(old('new_author_name')) ? old('new_author_name') : ''" maxlength="191" :required="old('author_id') === 'new'" aria-describedby="new-author-help new_author_name-error" />
                    <p class="field-help" id="new-author-help">Required when adding a new author. Their name will be saved with this book and added to the list.</p>
                    <x-input-error :messages="$errors->get('new_author_name')" id="new_author_name-error" class="mt-1.5" />
                </div>
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
            <div data-cover-upload>
                <x-input-label for="cover_image" value="Cover image (optional)" />
                <x-text-input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="cover-help cover_image-error" class="border p-2 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:font-semibold file:text-brand-800" />
                <p class="field-help" id="cover-help">Choose a JPG, PNG, or WebP image, up to 2 MB.@if ($book->exists && $book->cover_path) Leave this empty to keep the current cover.@endif</p>
                <x-input-error :messages="$errors->get('cover_image')" id="cover_image-error" class="mt-1.5" />
                @if ($errors->any())<p class="mt-1.5 text-sm text-stone-600">If you selected a cover, choose it again before saving.</p>@endif
                <div @class(['mt-4 rounded-lg border border-stone-200 bg-stone-50 p-4', 'hidden' => ! $book->cover_path]) data-cover-preview>
                    <div class="flex items-center gap-4">
                        <img @if ($book->cover_path) src="{{ asset($book->cover_path) }}" @endif alt="Cover preview" class="h-36 w-24 shrink-0 rounded bg-white object-contain" data-cover-preview-image>
                        <div class="min-w-0 space-y-2">
                            <p class="break-all text-sm font-medium text-stone-800" data-cover-filename>Current cover</p>
                            <button type="button" class="text-link hidden" data-cover-reset>Cancel selection</button>
                        </div>
                    </div>
                </div>
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
