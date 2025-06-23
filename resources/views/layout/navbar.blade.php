<header>
    <nav class="container">
        <a href="#" class="logo">{{ env('APP_NAME') }}</a>
        <ul class="nav-links">
            @if (auth()->check())
                <li><a href="{{route('platform.logout')}}">Sign out</a></li>
                <li><a href="{{route('platform.main')}}">Dashboard</a></li>
            @else
                <li><a href="{{route('platform.login')}}">Sign in</a></li>
                <li><a href="{{route('platform.register')}}">Sign up</a></li>
            @endif
        </ul>
        {{-- <a href="#" class="cta-button">Sign in</a>
        <a href="#" class="cta-button">Sign up</a> --}}
    </nav>
</header>
