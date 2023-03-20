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
            <div class="dropdown">
                <a href="#" class="d-block link-light text-decoration-none " data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img src="{{ Session::get('avatar') ? 'https://identity.eurofurence.org/storage/avatars/' : '' }}{{ Session::get('avatar') ?? asset('default.jpg') }}"
                        alt="mdo" width="40" height="40" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small shadow">
                    <li><a class="dropdown-item" href="https://identity.eurofurence.org/settings/profile">Your
                            Account</a></li>
                    <li><a class="dropdown-item"
                            href="https://identity.eurofurence.org/oauth2/sessions/logout?id_token_hint={{ Session::get('access_token')->getValues()['id_token'] }}">Logout</a>
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
