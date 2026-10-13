<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ $book->exists ? '✏️ Edit Book: ' . $book->title : '➕ Add New Book' }}
            </h2>
            <a href="{{ $book->exists ? route('books.show', $book) : route('books.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <form method="POST" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" class="space-y-6">
                @csrf
                @if ($book->exists)
                    @method('PUT')
                @endif

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700">Book Title</label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title', $book->title) }}"
                           placeholder="e.g. Clean Code: A Handbook of Agile Software Craftsmanship"
                           class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                {{-- Author Dropdown --}}
                <div>
                    <label for="author_id" class="block text-sm font-bold text-gray-700">Author</label>
                    <select name="author_id" id="author_id" required
                            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select Author --</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ISBN & Published Year Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="isbn" class="block text-sm font-bold text-gray-700">ISBN (13 digits)</label>
                        <input type="text" name="isbn" id="isbn" required maxlength="13"
                               value="{{ old('isbn', $book->isbn) }}"
                               placeholder="9780132350884"
                               class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono">
                    </div>

                    <div>
                        <label for="published_year" class="block text-sm font-bold text-gray-700">Published Year</label>
                        <input type="number" name="published_year" id="published_year" required min="1500" max="{{ date('Y') + 1 }}"
                               value="{{ old('published_year', $book->published_year) }}"
                               class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                {{-- Cover Image Path / URL --}}
                <div>
                    <label for="cover_path" class="block text-sm font-bold text-gray-700">Book Cover Image Path or URL (Optional)</label>
                    <input type="text" name="cover_path" id="cover_path"
                           value="{{ old('cover_path', $book->cover_path) }}"
                           placeholder="e.g. /images/books/isbn_9780132350884.jpg or web image link"
                           class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Provide a local image path like <code class="bg-gray-100 px-1 py-0.5 rounded">/images/books/isbn_9780132350884.jpg</code> or an external image URL.</p>
                </div>

                {{-- Reference Book Checkbox --}}
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <label class="inline-flex items-center space-x-3 cursor-pointer">
                        <input type="hidden" name="is_reference" value="0">
                        <input type="checkbox" name="is_reference" value="1"
                               class="rounded border-amber-300 text-amber-600 shadow-sm focus:ring-amber-500"
                               @checked(old('is_reference', $book->is_reference))>
                        <div>
                            <span class="text-sm font-bold text-amber-900">Mark as Reference Material Only</span>
                            <p class="text-xs text-amber-700">Reference books cannot be checked out or borrowed for home reading.</p>
                        </div>
                    </label>
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                    <a href="{{ $book->exists ? route('books.show', $book) : route('books.index') }}"
                       class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition">
                        {{ $book->exists ? 'Update Book' : 'Save Book' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
