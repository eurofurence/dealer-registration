<!-- Page Heading -->
<nav class="py-2 bg-primary navbar-dark border-bottom">
    <div class="container d-flex flex-wrap">
        <ul class="nav me-auto">

            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link link-light px-2 active"
                    aria-current="page">Dashboard</a>
            </li>

            <li class="nav-item">
                <a href="{{ config('ef.dealers_tos_url') }}" class="nav-link link-light px-2" aria-current="page">Dealers’
                    Den Rules & Information</a>
            </li>
        </ul>
        <ul class="nav">
            <div class="dropdown nav-item">
                <a href="#" class="d-block link-light text-decoration-none" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img src="{{ Session::get('avatar') ?? asset('default.jpg') }}"
                        alt="{{ Session::get('name') ?? 'avatar' }}" width="40" height="40" class="rounded-circle">
                    <span class="align-middle">{{ Session::get('name') ?? 'User' }}</span>
                </a>
                <ul class="dropdown-menu text-small shadow">
                    <li><a class="dropdown-item" href="https://identity.eurofurence.org/settings/profile">Your
                            Account</a></li>
                    <li><a class="dropdown-item"
                            href="{{ route('auth.frontchannel-logout') }}">Logout</a>
                    </li>
                </ul>
            </div>
        </ul>
    </div>
</nav>
<header class="py-3 px-2 mb-4 border-bottom bg-white">
    <div class="container">
        <div class="d-flex align-items-baseline mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
            <span class="fs-4 px-2">Eurofurence Dealers' Den Registration System</span>
            <span class="small">v1</span>
        </div>
    </div>
</header>
