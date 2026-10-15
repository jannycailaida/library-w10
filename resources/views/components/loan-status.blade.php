@props(['borrowedAt', 'returnedAt' => null])
@php($dueDate = \Carbon\Carbon::parse($borrowedAt)->addDays(14))
@if ($returnedAt)
    <span class="badge-available">Returned</span>
@elseif (now()->gt($dueDate))
    <span class="badge-overdue">Overdue</span>
@else
    <span class="badge-active">On loan</span>
@endif
