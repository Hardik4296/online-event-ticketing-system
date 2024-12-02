<!-- ***** Preloader Start ***** -->
<div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>
<!-- ***** Preloader End ***** -->

<!-- ***** Header Area Start ***** -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="{{route('home')}}" class="logo">Enjoy<em>Event</em></a>
                    <!-- ***** Logo End ***** -->

                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li><a href="{{route('home')}}">Home</a></li>

                        @auth
                            @if(auth()->user()->role == "organizer")
                                <li><a href="{{route('organizer.dashboard')}}" class="active" >Dashboard</a></li>
                            @endif
                        @endauth

                        <li><a href="{{route('organizer.events.list')}}">My Events</a></li>

                        @guest
                            <li>
                                <a href="#" data-toggle="modal" data-target="#loginModal">
                                    Login Now!
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </li>
                        @endguest

                        @auth
                            <!-- User Profile Dropdown -->
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random&size=64" alt="Profile" class="rounded-circle mr-2" width="30" height="30">
                                        {{ Auth::user()->name }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                        <div class="dropdown-item text-button">
                                            <form method="POST" action="{{ route('auth.logout') }}" id="logout-form">
                                                @csrf
                                                <button type="submit">Logout</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        @endauth
                    </ul>
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>

@guest
    <!-- Login Modal -->
    @include('auth.login')

    <!-- Signup Modal -->
    @include('auth.signup')
@endguest
