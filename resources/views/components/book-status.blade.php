@props(['book'])
@if ($book->is_reference)
    <span class="badge-reference">Reference · library use only</span>
@elseif ($book->isBorrowed())
    <span class="badge-active">On loan</span>
@else
    <span class="badge-available"><span class="h-1.5 w-1.5 rounded-full bg-brand-700" aria-hidden="true"></span>Available</span>
@endif
