@php
    $navigation = [];
    if (auth()->check()) {
        $navigation[] = ['label' => 'Overview', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')];
    }
    $navigation[] = ['label' => 'Catalogue', 'route' => 'books.index', 'active' => request()->routeIs('books.*')];
    if (auth()->check()) {
        $navigation[] = ['label' => 'My loans', 'route' => 'my.loans', 'active' => request()->routeIs('my.loans')];
        if (auth()->user()->hasRole('librarian', 'admin')) {
            $navigation[] = ['label' => 'Circulation desk', 'route' => 'desk', 'active' => request()->routeIs('desk')];
        }
        if (auth()->user()->can('manage-users')) {
            $navigation[] = ['label' => 'Users', 'route' => 'admin.users.index', 'active' => request()->routeIs('admin.users.*')];
        }
    }
@endphp
<header class="border-b border-stone-200 bg-white" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="mx-auto flex min-h-[4.5rem] max-w-7xl items-center gap-6 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('books.index') }}" class="flex shrink-0 items-center gap-2.5 text-lg font-bold tracking-tight text-stone-900" aria-label="LibraryHub catalogue">
            <x-application-logo class="h-8 w-8 text-brand-700" />LibraryHub
        </a>
        <nav class="hidden flex-1 items-center gap-1 lg:flex" aria-label="Main navigation">
            @foreach ($navigation as $item)
                <a href="{{ route($item['route']) }}" @class(['nav-item', 'nav-active' => $item['active']]) @if($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <div class="ml-auto hidden items-center gap-2 lg:flex">
            @auth
                <a href="{{ route('profile.edit') }}" @class(['nav-item max-w-[10rem] gap-2', 'nav-active' => request()->routeIs('profile.*')]) @if(request()->routeIs('profile.*')) aria-current="page" @endif>
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-800" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <span class="truncate">{{ auth()->user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="nav-item">Sign out</button></form>
            @else
                <a href="{{ route('login') }}" class="nav-item">Sign in</a>
                <a href="{{ route('register') }}" class="btn-primary">Join the library</a>
            @endauth
        </div>
        <button type="button" class="btn-secondary ml-auto px-3 lg:hidden" @click="open = !open" :aria-expanded="open" aria-controls="mobile-navigation" aria-label="Toggle navigation menu">
            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span>Menu</span>
        </button>
    </div>
    <nav id="mobile-navigation" x-show="open" x-cloak class="border-t border-stone-200 p-4 lg:hidden" aria-label="Mobile navigation">
        <div class="grid gap-1 sm:grid-cols-2">
            @foreach ($navigation as $item)
                <a href="{{ route($item['route']) }}" @class(['nav-item', 'nav-active' => $item['active']]) @if($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
            @auth
                <a href="{{ route('profile.edit') }}" class="nav-item">My profile <span class="ml-2 text-xs">({{ ucfirst(auth()->user()->role) }})</span></a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="nav-item w-full">Sign out</button></form>
            @else
                <a href="{{ route('login') }}" class="nav-item">Sign in</a>
                <a href="{{ route('register') }}" class="nav-item">Join the library</a>
            @endauth
        </div>
    </nav>
</header>
