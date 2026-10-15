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
            'author_id' => ['required', 'integer', 'exists:authors,id'],
            'isbn' => ['required', 'string', 'regex:/^[0-9]{13}$/', Rule::unique('books')->ignore($this->route('book'))],
            'published_year' => ['required', 'integer', 'between:1500,'.(now()->year + 1)],
            'is_reference' => ['sometimes', 'boolean'],
            'cover_path' => ['nullable', 'string', 'max:191', 'regex:~^(?:/(?!/)[^\s]*|https?://[^\s]+)$~i'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'isbn.regex' => 'Enter an ISBN containing exactly 13 digits.',
            'cover_path.regex' => 'Use a local path starting with / or an http:// or https:// image URL.',
        ];
    }
}
