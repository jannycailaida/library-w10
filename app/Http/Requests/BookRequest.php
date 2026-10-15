<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');

        return $book instanceof Book
            ? $this->user()->can('update', $book)
            : $this->user()->can('create', Book::class);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'author_id' => ['bail', 'required', Rule::when($this->input('author_id') !== 'new', ['integer', 'exists:authors,id'])],
            'new_author_name' => ['exclude_unless:author_id,new', 'required', 'string', 'max:191'],
            'isbn' => ['required', 'string', 'regex:/^[0-9]{13}$/', Rule::unique('books')->ignore($this->route('book'))],
            'published_year' => ['required', 'integer', 'between:1500,'.(now()->year + 1)],
            'is_reference' => ['sometimes', 'boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'author_id.required' => 'Select an author or choose "Add a new author".',
            'author_id.integer' => 'Select an author from the list or choose "Add a new author".',
            'author_id.exists' => 'Select an author from the list or choose "Add a new author".',
            'new_author_name.required' => 'Enter the new author\'s name.',
            'new_author_name.string' => 'Enter the new author\'s name as text.',
            'new_author_name.max' => 'The author\'s name must be 191 characters or fewer.',
            'isbn.regex' => 'Enter an ISBN containing exactly 13 digits.',
            'cover_image.image' => 'Choose a JPG, PNG, or WebP image.',
            'cover_image.mimes' => 'Choose a JPG, PNG, or WebP image.',
            'cover_image.max' => 'The cover image must be 2 MB or smaller.',
            'cover_image.uploaded' => 'The cover image could not be uploaded. Choose an image of 2 MB or smaller.',
        ];
    }
}
