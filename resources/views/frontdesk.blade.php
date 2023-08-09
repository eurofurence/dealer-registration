<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Frontdesk - Dealer's Registration - Eurofurence</title>

    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="vh-100 vw-100">
        <!-- Page Heading -->
        <nav class="bg-primary navbar-dark border-bottom w-100" style="height: 5% !important;">
            <div class="container d-flex flex-wrap">
                <ul class="nav me-auto">

                    <li class="nav-item">
                        <a href="{{ route('frontdesk') }}" class="nav-link link-light px-4 active"
                            aria-current="page">Frontdesk</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('filament.pages.dashboard') }}" class="nav-link link-light px-4"
                            aria-current="page">Admin</a>
                    </li>
                </ul>
                <ul class="nav">
                    <li class="nav-item">
                        <span class="nav-link link-light">{{ $user->name }}</span>
                    </li>
                    <li class="nav-item">
                        <img src="{{ Session::get('avatar') ? 'https://identity.eurofurence.org/storage/avatars/' : '' }}{{ Session::get('avatar') ?? asset('default.jpg') }}"
                            alt="mdo" width="40" height="40" class="rounded-circle">
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('auth.frontchannel-logout') }}" class="nav-link link-danger px-2">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>


        <!-- Page Content -->
        <div class="container mw-100" style="height: 95% !important;">
            <div class="row h-100">
                <div class="col-3 h-100">
                    <form method="post" action="{{ route('frontdesk.search') }}" name="search">
                        <input type="text" class="form-control my-2 text-center fs-1" id="reg-id" name="reg_id">
                        @csrf
                    </form>
                    <div class="container text-center h-75">
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">7</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">8</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">9</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">4</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">5</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">6</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">1</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">2</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">3</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button" class="btn btn-danger align-self-center h-100 border fs-1"
                                onclick="document.forms.search.reset()">✗</button>
                            <button type="button"
                                onclick="document.forms.search.elements.reg_id.value = document.forms.search.elements.reg_id.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">0</button>
                            <button type="button" onclick="document.forms.search.submit()"
                                class="btn btn-success align-self-center h-100 border fs-1">↵</button>
                        </div>
                    </div>
                </div>
                <div class="col-6 fs-3">
                    @if ($application === false)
                        <div class="card text-bg-danger text-center">
                            <div class="card-body">
                                User not found
                            </div>
                        </div>
                    @elseif ($application === null)
                        <div class="card text-bg-danger text-center">
                            <div class="card-body">
                                No application attached to user
                            </div>
                        </div>
                    @else
                        @if ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
                            <div class="card text-bg-success text-center">
                                <div class="card-body">
                                    {{ $application->status->name }} – Ready for Check-In
                                </div>
                            </div>
                        @elseif ($application->status === \App\Enums\ApplicationStatus::CheckedIn)
                            <div class="card text-bg-info text-center">
                                <div class="card-body">
                                    {{ $application->status->name }} –
                                    Ready for Check-Out
                                </div>
                            </div>
                        @else
                            <div class="card text-bg-warning text-center">
                                <div class="card-body">
                                    {{ $application->status->name }}
                                </div>
                            </div>
                        @endif
                        <div class="card">
                            <div class="card-body">
                                {{ $application->type->name }}
                                {{ $application->user()->first()->name }}
                                {{ $application->is_afterdark }}
                                {{ $application->table_number }}
                                {{ $application->assignedTable->first()->name }}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col h-100 overflow-auto">
                    @if ($application)
                        <form method="post" action="{{ route('frontdesk.comment') }}" name="comment">
                            <div class="card">
                                <div class="card-body">
                                    <textarea class="form-control my-2 fs-4 w-100" id="comment" name="comment"></textarea>
                                </div>
                                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                                    <div>
                                        <input class="form-check-input fs-3" type="checkbox" value=""
                                            name="admin_only" id="adminOnly">
                                        <label class="form-check-label fs-3" for="adminOnly">
                                            admin-only
                                        </label>
                                    </div>
                                    <div class="">
                                        <button class="btn btn-danger mx-2 fs-3" type="reset">Clear</button>
                                        <button class="btn btn-success mx-2 fs-3" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            @csrf
                        </form>
                        @foreach ($application->comments()->get() as $comment)
                            @can('view', $comment)
                                <div class="card my-2">
                                    <div class="card-body fs-4">
                                        {{ $comment->text }}
                                    </div>
                                    <div class="card-footer text-muted fs-5">
                                        by {{ $comment->author()->first()->name }} on {{ $comment->created_at }}
                                        @if ($comment->admin_only)
                                            <span class="badge bg-warning text-dark">Admin only!</span>
                                        @endif
                                    </div>
                                </div>
                            @endcan
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>
