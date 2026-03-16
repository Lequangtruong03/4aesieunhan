<!-- Header section -->
<nav class="header navbar navbar-expand-lg navbar-dark shadow" style="background-color: #2e292e">
    <div class="container">
        <a class="navbar-brand" href="#">HMCinema</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
            <ul class="navbar-nav text-uppercase mx-auto">
                <li class="nav-item">
                    <a class="nav-link @yield('movies')" href="/movies" role="button">@lang('lang.sortby_movies')</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link @yield('schedules')" href="/schedulesByMovie">@lang('lang.sortby_schedules')</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link @yield('events')" href="/events">@lang('lang.news_events')</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link @yield('support')" href="/contact">@lang('lang.contact')/@lang('lang.support')</a>
                </li>

            </ul>
            @if(Auth::check())
                <div class="nav-item dropdown mx-2">
                    <a class="nav-link link-warning font-weight-bold text-decoration-underline dropdown" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-id-card me-sm-1"></i>
                        <span class="d-sm-inline d-none">{{ Auth::user()->fullName }}</span>
                    </a>
                    <ul class="dropdown-menu shadow" style="background-color: #2e292e">
                        <li><a class="dropdown-item link-light" href="/profile">@lang('lang.profile')</a></li>
                        <li><a class="dropdown-item link-light" href="/signOut">@lang('lang.signout')</a></li>
                    </ul>
                </div>
            @else
                <div class="nav-item mx-2 float-end">
                    <a class="nav-link link-warning text-decoration-underline" href="#loginModal" data-bs-toggle="modal" data-bs-target="#loginModal">
                        @lang('lang.signin')
                    </a>
                </div>
            @endif

        </div>

    </div>
    <div class=" mx-2 dropdown float-end">
        <button class="btn btn-link text-decoration-none link-light" href="#"
                data-bs-toggle="dropdown" aria-expanded="false">
            @lang('lang.lang'): <img class="rounded ms-1" style="max-width: 30px" src="images/language/@lang('lang.flag').png" alt="vietnamese">
        </button>
        <ul class="dropdown-menu shadow dropdown-menu-end" style="background-color: #f5f5f5">
            <li>
                <a class="dropdown-item" href="lang/en">
                    <img class="rounded me-1" style="max-width: 30px" src="images/language/united-states.png" alt="engligh">
                    @lang('lang.en')
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="lang/vi">
                    <img class="rounded me-1" style="max-width: 30px" src="images/language/vietnam.png" alt="vietnamese">
                    @lang('lang.vi')
                </a>
            </li>
        </ul>
    </div>
</nav>
