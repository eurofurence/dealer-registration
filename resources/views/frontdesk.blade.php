<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Frontdesk - Dealer's Registration - Eurofurence</title>

    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
        #navbar {
            height: 9.99% !important;
        }

        @media (max-width: 767.98px) {

            /* Avoid scrolling once overflowable columns get wrapped into a single column. */
            .overflow-auto.col-md.mh-100,
            .overflow-auto.col-md-6.mh-100 {
                max-height: none !important;
            }

            #navbar {
                height: auto !important;
                align-items: center !important;
            }
        }
    </style>
</head>

<body class="bg-light">
    <div class="vh-100 vw-100">
        <!-- Navigation Bar -->
        <nav id="navbar" class="bg-primary navbar-dark text-light border-bottom">
            <div class="d-flex align-items-center h-100 mx-3">
                <a href="{{ route('frontdesk') }}"
                    class="fs-2 me-auto d-block text-light text-decoration-none flex-fill">Dealers' Den
                    Frontdesk</a>
                <div class="my-1 z-1">
                    <span class="fs-5 align-middle">{{ $user->name }}</span>
                    <img src="{{ Session::get('avatar') ? 'https://identity.eurofurence.org/storage/avatars/' : '' }}{{ Session::get('avatar') ?? asset('default.jpg') }}"
                        alt="mdo" width="40" height="40" class="rounded-circle">
                    @if ($user->isAdmin())
                        <a href="{{ route('filament.pages.dashboard') }}" class="btn btn-secondary">Admin</a>
                    @endif
                    <a href="{{ route('auth.frontchannel-logout') }}" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container mw-100" style="height: 90% !important;">
            <div class="row h-100">

                <!-- Numpad & Search Column -->
                <div class="col-md-3 mh-100">
                    <form method="get" action="{{ route('frontdesk') }}" name="search">
                        <input type="text" class="form-control my-2 text-center fs-1" id="search" name="search"
                            autofocus>
                    </form>
                    <div class="container text-center h-75">
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">7</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">8</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">9</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">4</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">5</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">6</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">1</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">2</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">3</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button" class="btn btn-danger align-self-center h-100 border fs-1"
                                onclick="document.forms.search.reset();document.forms.search.submit()">✗</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1">0</button>
                            <button type="button" onclick="document.forms.search.submit()"
                                class="btn btn-success align-self-center h-100 border fs-1">↵</button>
                        </div>
                    </div>
                </div>

                <!-- Application Column -->
                <div class="col-md-6 fs-3 mh-100 overflow-auto">
                    @if (empty($search))
                        <div class="card my-2">
                            <div class="card-header text-center fs-3">
                                Welcome to the Dealers' Den Frontdesk!
                            </div>
                            <div class="card-body fs-4">
                                You can search Dealers, Shares or Assistants by:
                                <ul>
                                    <li><strong>registration ID</strong> (exact match; no checksum!),</li>
                                    <li><strong>attendee nickname</strong> (supports <code>%</code> as wildcard; not
                                        case-sensitive),</li>
                                    <li><strong>table number</strong> (exact match) or</li>
                                    <li><strong>display name</strong> (supports <code>%</code> as wildcard; not
                                        case-sensitive).</li>
                                </ul>
                            </div>
                            <div class="card-footer fs-5">
                                The first matching result will be loaded automatically. If it's not who you were looking
                                for, please try making your search more specific.
                            </div>
                        </div>
                    @elseif ($applicant === null)
                        <div class="card text-bg-danger text-center my-2">
                            <div class="card-body">
                                User or application <em>"{{ $search }}"</em> not found.
                            </div>
                        </div>
                    @else
                        @if ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
                            <div class="card text-bg-success text-center my-2">
                                <div class="card-body">
                                    {{ $application->status->name }} – Ready for Check-In
                                </div>
                            </div>
                        @elseif ($application->status === \App\Enums\ApplicationStatus::CheckedIn)
                            <div class="card text-bg-info text-center my-2">
                                <div class="card-body">
                                    {{ $application->status->name }} –
                                    Ready for Check-Out
                                </div>
                            </div>
                        @else
                            <div class="card text-bg-warning text-center my-2">
                                <div class="card-body">
                                    {{ $application->status->name }}
                                </div>
                            </div>
                        @endif
                        <div class="accordion my-2" id="applicationData">
                            <div class="accordion-item">
                                <span class="accordion-header">
                                    <button class="accordion-button d-flex align-items-center fs-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#applicationDataGeneral"
                                        aria-expanded="true" aria-controls="applicationDataGeneral">
                                        {{ $applicant->name }} ({{ $applicant->reg_id }})
                                        @switch($application->type)
                                            @case(\App\Enums\ApplicationType::Dealer)
                                                <span class="badge text-bg-success mx-2">{{ $application->type->name }}</span>
                                            @break

                                            @case(\App\Enums\ApplicationType::Share)
                                                <span class="badge text-bg-warning mx-2">{{ $application->type->name }}</span>
                                            @break

                                            @case(\App\Enums\ApplicationType::Assistant)
                                                <span class="badge text-bg-info mx-2">{{ $application->type->name }}</span>
                                            @break

                                            @default
                                                <span class="badge text-bg-danger mx-2">Unknown Type!</span>
                                        @endswitch
                                        @if ($application->is_afterdark)
                                            <span class="badge text-bg-dark">ADD</span>
                                        @endif
                                    </button>
                                </span>
                                <div id="applicationDataGeneral" class="accordion-collapse collapse show"
                                    data-bs-parent="#applicationData">
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label for="displayName" class="form-label">Display Name</label>
                                            <input type="text" readonly class="form-control fs-4" id="displayName"
                                                value="{{ $application->type !== \App\Enums\ApplicationType::Assistant
                                                    ? (empty($application->display_name)
                                                        ? $applicant->name
                                                        : $application->display_name)
                                                    : (empty($parent->display_name)
                                                        ? $parent->user->name
                                                        : $parent->display_name) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="table" class="form-label">Table</label>
                                            <input type="text" readonly class="form-control fs-4" id="table"
                                                value="{{ $application->table_number }}{{ $table ? ' – ' . $table->name : '' }}">
                                        </div>

                                        <!-- Related Applications -->
                                        <!-- Parent Dealership -->
                                        @if ($parent)
                                            <div class="mb-3">
                                                <span class="form-label">Dealership</label>
                                                    <x-frontdesk.applicationbutton :applicant="$parentApplicant"
                                                        :application="$parent"></x-frontdesk.applicationbutton>
                                            </div>
                                        @endif

                                        <!-- Shares -->
                                        @if (!empty($shares))
                                            <div class="mb-3">
                                                <span class="form-label">Shares</label>
                                                    @foreach ($shares as $share)
                                                        @php($shareApplicant = $share->user)
                                                        <x-frontdesk.applicationbutton :applicant="$shareApplicant"
                                                            :application="$share"></x-frontdesk.applicationbutton>
                                                    @endforeach
                                            </div>
                                        @endif

                                        <!-- Assistants -->
                                        @if (!empty($assistants))
                                            <div class="mb-3">
                                                <span class="form-label">Assistants</label>
                                                    @foreach ($assistants as $assistant)
                                                        @php($assistantApplicant = $assistant->user)
                                                        <x-frontdesk.applicationbutton :applicant="$assistantApplicant"
                                                            :application="$assistant"></x-frontdesk.applicationbutton>
                                                    @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fs-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#applicationDataAddition"
                                        aria-expanded="false" aria-controls="applicationDataAddition">
                                        Additional Information
                                    </button>
                                </h2>
                                <div id="applicationDataAddition" class="accordion-collapse collapse"
                                    data-bs-parent="#applicationData">
                                    <div class="accordion-body">
                                        <div class="alert alert-info">Work in Progress</div>
                                    </div>
                                </div>
                            </div>
                            @if ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fs-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#checkIn" aria-expanded="false"
                                            aria-controls="checkIn">
                                            Check-In Checklist
                                        </button>
                                    </h2>
                                    <div id="checkIn" class="accordion-collapse collapse"
                                        data-bs-parent="#applicationData">
                                        <div class="accordion-body">
                                            <div class="alert alert-info">Work in Progress</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($application->status === \App\Enums\ApplicationStatus::CheckedIn)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fs-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#checkOut"
                                            aria-expanded="false" aria-controls="checkOut">
                                            Check-Out Checklist
                                        </button>
                                    </h2>
                                    <div id="checkOut" class="accordion-collapse collapse"
                                        data-bs-parent="#checkOut">
                                        <div class="accordion-body">
                                            <div class="alert alert-info">Work in Progress</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Comments Column -->
                <div class="col-md mh-100 overflow-auto">
                    <form method="post" action="{{ route('frontdesk.comment') }}" name="comment">
                        <div class="card my-2">
                            <div class="card-header fs-3">
                                Comments
                            </div>
                            <div class="card-body">
                                <textarea class="form-control my-2 fs-4 w-100 @error('comment') is-invalid @enderror" id="comment" name="comment" @disabled(empty($application))>{{ old('_token') ? old('comment') : isset($comment) && $comment?->text }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div
                                class="card-footer text-muted d-flex justify-content-between align-items-center text-center">
                                <div class="">
                                    <input class="form-check-input fs-4" type="checkbox"
                                        name="admin_only" id="admin_only" @checked(old('_token') ? old('admin_only') : isset($comment) && $comment?->admin_only === true) @disabled(empty($application))>
                                    <label class="form-check-label fs-4" for="admin_only">
                                        admin-only
                                    </label>
                                </div>
                                <div class="">
                                    <button class="btn btn-danger mx-2 fs-3" type="reset"
                                        @disabled(empty($application))>✗</button>
                                    <button class="btn btn-success mx-2 fs-3" type="submit"
                                        @disabled(empty($application))>↵</button>
                                </div>
                            </div>
                        </div>
                        @csrf
                        <input type="hidden" name="application" value="{{ $application ? $application->id : ''}}">
                    </form>
                    @if ($application)
                        @foreach ($application->comments()->orderBy('created_at', 'desc')->get() as $comment)
                            @can('view', $comment)
                                <div class="card my-2">
                                    <div class="card-body fs-4">
                                        {{ $comment->text }}
                                    </div>
                                    <div class="card-footer text-muted fs-5">
                                        by {{ $comment->author->name }} on {{ $comment->created_at }}
                                        @if ($comment->admin_only)
                                            <span class="badge bg-warning text-dark">Admin only!</span>
                                        @endif
                                    </div>
                                </div>
                            @endcan
                        @endforeach
                    @else
                        <div class="card my-2">
                            <div class="card-body fs-4">
                                No application selected.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>