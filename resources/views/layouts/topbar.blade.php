<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
    <div class="container-fluid">
        {{-- Sidebar toggle button for small screens --}}
        @auth
            <button class="btn d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        @endauth

        <a class="navbar-brand" href="#">{{ config('app.name') }}</a>

        @auth
            <div class="collapse navbar-collapse" id="topbarMenu">
                <ul class="navbar-nav navbar-expand-lg ms-auto sticky-top">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topbarProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ strtok(Auth::user()->name, ' ') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="topbarProfileDropdown">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        {{ __('auth.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</nav>
