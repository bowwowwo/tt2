<nav class="navbar navbar-dark" style="background-color:#AE7D9A;">
    <div class="container-fluid">
        <div class="row w-100 align-items-center g-0">

            <div class="col text-start"> {{-- hamburger --}}
                <button
                    class="navbar-toggler border-0"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#sideMenu"
                    aria-controls="sideMenu"
                    aria-label="Menu"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="col text-center">
                <a
                    href="@auth {{ route('events.index') }} @else {{ route('landing') }} @endauth"
                    class="fw-semibold fs-4 bg-light border border-2 border-dark rounded px-2 py-1 d-inline-block text-decoration-none text-dark"
                >
                    Study Planner
                </a>
            </div>

            <div class="col text-end">
                @guest
                    <a class="btn btn-outline-light btn-sm me-2" href="{{ route('login') }}">
                        Login
                    </a>

                    <a class="btn btn-light btn-sm" href="{{ route('register') }}">
                        Register
                    </a>
                @endguest

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>

        </div>
    </div>
</nav>

{{-- //^ collapsible part --}}
<div
    class="offcanvas offcanvas-start"
    tabindex="-1"
    id="sideMenu"
    aria-labelledby="sideMenuLabel"
>
    <div class="offcanvas-header pt-4" style="background-color:#AE7D9A;">

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
        {{-- //* top--}}
        <div class="d-flex flex-column gap-2">

            @auth
                <a href="{{ route('landing') }}" class="btn btn-outline-dark text-start">
                    Main page
                </a>

                <a href="{{ route('events.index') }}" class="btn btn-outline-dark text-start">
                    Upcoming Events
                </a>

                <a href="#" class="btn btn-outline-dark text-start">
                    Schedules
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-dark w-100 text-start">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('landing') }}" class="btn btn-outline-dark text-start">
                    Home
                </a>

                <a href="{{ route('login') }}" class="btn btn-outline-dark text-start">
                    Login
                </a>

                <a href="{{ route('register') }}" class="btn btn-dark text-start">
                    Register
                </a>
            @endguest

        </div>

        {{-- //* bottom--}}
        <div class="mt-auto border-top pt-3 d-flex flex-column gap-2">

            @auth
                <a href="#" class="btn btn-outline-dark text-start">
                    Profile
                </a>
            @endauth

            <div class="dropup">
                <button
                    class="btn btn-outline-dark dropdown-toggle w-100 text-start"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    Switch Language
                </button>

                <ul class="dropdown-menu w-100">
                    <li>
                        <a class="dropdown-item" href="#">
                            English
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            Latvian
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>