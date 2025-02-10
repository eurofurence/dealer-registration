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

        .form-check-input.is-invalid~.form-check-label {
            font-weight: bold;
        }

        button.check-button.btn-primary::before {
            content: '✅';
            margin-right: 0.5ex;
        }

        button.check-button.btn-secondary::before {
            content: '❌';
            margin-right: 0.5ex;
        }

        #more-power {
            display: none;
        }

        #more-power:target {
            display: block;
        }

        .link-unstyled,
        .link-unstyled:link,
        .link-unstyled:hover {
            color: inherit;
            text-decoration: inherit;
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
    <!--
    Tab Index:
        1 -> Search field
        2 -> Comment inputs or Check-In/Check-Out controls if visible
        3 -> Accordion tabs
    -->
    <div class="vh-100 vw-100">
        <!-- Navigation Bar -->
        <nav id="navbar" class="bg-primary navbar-dark text-light border-bottom">
            <div class="d-flex align-items-center h-100 mx-3">
                <a href="{{ route('frontdesk') }}"
                    class="fs-2 me-auto d-block text-light text-decoration-none flex-fill">Dealers' Den
                    Frontdesk</a>
                <div class="my-1 z-1">
                    <span class="fs-5 align-middle">{{ $user->name }}</span>
                    @if (empty(Session::get('avatar')))
                        <x-heroicon-s-user-circle width="40" height="40" class="rounded-circle" />
                    @else
                        <img src="{{ Session::get('avatar') }}" alt="{{ Session::get('name') ?? 'avatar' }}"
                            width="40" height="40" class="rounded-circle">
                    @endif
                    @if ($user->isAdmin())
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-secondary">Admin</a>
                    @endif
                    <a href="https://app.eurofurence.org/tools/dealers.html" target="_blank"
                        class="btn btn-secondary">Dealer Profiles ⎋</a>
                    <a href="{{ route('auth.frontchannel-logout') }}" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container mw-100" style="height: 90% !important;">
            <div class="row h-100">

                <!-- Numpad & Search Column -->
                <div class="col-md-3 mh-100">
                    <form method="get" action="{{ route('frontdesk') }}" name="search" autocomplete="off">
                        <div class="container" style="height: 8% !important">
                            <div class="row row-cols-3 h-100">
                                <button type="button"
                                    onclick="document.forms.search.elements.type.value = 'default'; document.forms.search.submit();"
                                    class="btn h-100 border align-self-center @if ($type === 'default') btn-primary @else btn-secondary @endif"
                                    tabindex="-1">Def</button>
                                <button type="button"
                                    onclick="document.forms.search.elements.type.value = 'name'; document.forms.search.submit();"
                                    class="btn h-100 border align-self-center @if ($type === 'name') btn-primary @else btn-secondary @endif"
                                    tabindex="-1">Name</button>
                                <button type="button"
                                    onclick="document.forms.search.elements.type.value = 'keyword'; document.forms.search.submit();"
                                    class="btn h-100 border align-self-center @if ($type === 'keyword') btn-primary @else btn-secondary @endif"
                                    tabindex="-1">Key</button>
                            </div>
                        </div>
                        <input type="text" class="form-control my-2 text-center fs-1" id="search" name="search"
                            tabindex="1" autofocus>
                        <input type="hidden" name="type" value="{{ app('request')->input('type') ?? 'default' }}">
                    </form>
                    <div class="container text-center" style="height: 68% !important">
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">7</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">8</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">9</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">4</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">5</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">6</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">1</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">2</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">3</button>
                        </div>
                        <div class="row row-cols-3 h-25">
                            <button type="button" class="btn btn-danger align-self-center h-100 border fs-1"
                                onclick="document.forms.search.reset();document.forms.search.elements.type.value='default';document.forms.search.submit()"
                                tabindex="-1">✗</button>
                            <button type="button"
                                onclick="document.forms.search.elements.search.value = document.forms.search.elements.search.value + this.innerHTML;"
                                class="btn btn-primary align-self-center h-100 border fs-1" tabindex="-1">0</button>
                            <button type="button" onclick="document.forms.search.submit()"
                                class="btn btn-success align-self-center h-100 border fs-1" tabindex="-1">↵</button>
                        </div>
                    </div>
                </div>

                <!-- Application Column -->
                <div class="col-md-6 fs-3 mh-100 overflow-auto">
                    @if (empty($search) && $type !== 'keyword')
                        <div class="card my-2">
                            <div class="card-header text-center fs-3">
                                Welcome to the Dealers' Den Frontdesk!
                            </div>
                            <div class="card-body fs-4">
                                <div class="fs-3"><strong>Def</strong>ault Mode</div>
                                Search Dealers, Shares or Assistants by:
                                <ul>
                                    <li><strong>registration ID</strong> (exact match; no checksum!),</li>
                                    <li><strong>attendee nickname</strong> (supports <code>%</code> as wildcard; not
                                        case-sensitive),</li>
                                    <li><strong>table number</strong> (exact match; slash (/) can be omitted) or</li>
                                    <li><strong>display name</strong> (supports <code>%</code> as wildcard; not
                                        case-sensitive).</li>
                                </ul>
                                <div class="fs-3"><strong>Name</strong> Mode</div>
                                Search for any part of a name or display name.
                                <div class="fs-3"><strong>Key</strong>word & Category Mode</div>
                                Search for part of a keyword/category name or select one from the list if
                                you provide no search text.
                            </div>
                            <div class="card-footer fs-5">
                                <strong>Def</strong>ault mode will always load the first matching result. If it's not
                                who you
                                were looking for, please try making your search more specific or use one of the other
                                search modes.
                            </div>
                        </div>
                    @elseif(empty($search) && $type === 'keyword')
                        <x-frontdesk.keywords :categories="$categories"></x-frontdesk.keywords>
                    @elseif ($applicant === null && ($applications === null || count($applications) === 0))
                        <div class="card text-bg-danger text-center my-2">
                            <div class="card-body">
                                No dealers, shares or assistants found for search query <em>"{{ $search }}"</em>
                                in mode <em>{{ $type }}</em>.
                            </div>
                        </div>
                    @elseif ($applicant !== null)
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

                        <!-- Application Data -->
                        <div class="accordion my-2" id="applicationData">

                            <!-- Main Application Data -->
                            <div class="accordion-item">
                                <span class="accordion-header">
                                    <button @class([
                                        'accordion-button',
                                        'fs-3',
                                        'd-flex',
                                        'align-items-center',
                                        'collapsed' =>
                                            $errors->hasBag('check-in') ||
                                            $errors->hasBag('check-out') ||
                                            $showAdditional,
                                    ]) type="button" data-bs-toggle="collapse"
                                        data-bs-target="#applicationDataGeneral"
                                        aria-expanded="{{ $errors->hasBag('check-in') || $errors->hasBag('check-out') || $showAdditional ? 'false' : 'true' }}"
                                        aria-controls="applicationDataGeneral" tabindex="3">
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
                                <div id="applicationDataGeneral" @class([
                                    'accordion-collapse',
                                    'collapse',
                                    'show' =>
                                        !$errors->hasBag('check-in') &&
                                        !$errors->hasBag('check-out') &&
                                        !$showAdditional,
                                ])
                                    data-bs-parent="#applicationData">
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label for="displayName" class="form-label">Display Name</label>
                                            <input type="text" readonly class="form-control fs-4" id="displayName"
                                                value="{{ $application->type !== \App\Enums\ApplicationType::Assistant
                                                    ? $application->getFullName()
                                                    : $parent->getFullName() }}"
                                                tabindex="-1">
                                        </div>
                                        <div class="mb-3">
                                            <label for="table" class="form-label">Table</label>
                                            <input type="text" readonly class="form-control fs-4" id="table"
                                                value="{{ $application->parent?->table_number ?? $application->table_number }}{{ $table ? ' – ' . $table->name : '' }}"
                                                tabindex="-1">
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

                            <!-- Additional Information -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button @class(['accordion-button', 'fs-3', 'collapsed' => !$showAdditional]) type="button" data-bs-toggle="collapse"
                                        data-bs-target="#applicationDataAdditional"
                                        aria-expanded="{{ $showAdditional ? 'true' : 'false' }}"
                                        aria-controls="applicationDataAdditional" tabindex="3">
                                        Additional Information
                                    </button>
                                </h2>
                                <div id="applicationDataAdditional" @class(['accordion-collapse', 'collapse', 'show' => $showAdditional])
                                    data-bs-parent="#applicationData">
                                    <div class="accordion-body">
                                        @if ($profile)
                                            <div class="mb-3 text-center">
                                                <div class="btn-group mx-auto form-input d-flex">
                                                    <button type="button" @class([
                                                        'btn',
                                                        'fs-4',
                                                        'check-button',
                                                        'btn-primary' => $profile->attends_thu,
                                                        'btn-secondary' => !$profile->attends_thu,
                                                    ])>Day 2
                                                        ({{ substr(config('convention.day_2_name'), 0, 3) }})</button>
                                                    <button type="button" @class([
                                                        'btn',
                                                        'fs-4',
                                                        'check-button',
                                                        'btn-primary' => $profile->attends_fri,
                                                        'btn-secondary' => !$profile->attends_fri,
                                                    ])>Day 3
                                                        ({{ substr(config('convention.day_3_name'), 0, 3) }})</button>
                                                    <button type="button" @class([
                                                        'btn',
                                                        'fs-4',
                                                        'check-button',
                                                        'btn-primary' => $profile->attends_sat,
                                                        'btn-secondary' => !$profile->attends_sat,
                                                    ])>Day 4
                                                        ({{ substr(config('convention.day_4_name'), 0, 3) }})</button>
                                                </div>
                                            </div>
                                            <div class="mb-3 d-flex justify-content-evenly">
                                                <button type="button" @class([
                                                    'btn',
                                                    'fs-4',
                                                    'check-button',
                                                    'btn-primary' => $application->is_afterdark,
                                                    'btn-secondary' => !$application->is_afterdark,
                                                ])>After
                                                    Dark</button>
                                                <button type="button" @class([
                                                    'btn',
                                                    'fs-4',
                                                    'check-button',
                                                    'btn-primary' => $application->is_wallseat,
                                                    'btn-secondary' => !$application->is_wallseat,
                                                ])>Wallseat</button>
                                                <button type="button" @class([
                                                    'btn',
                                                    'fs-4',
                                                    'check-button',
                                                    'btn-primary' => $application->is_power,
                                                    'btn-secondary' => !$application->is_power,
                                                ])><a
                                                        href="#more-power" class="link-unstyled">More Power</a><a
                                                        href="#"><img
                                                            src="{{ Vite::asset('resources/assets/more-power.gif') }}"
                                                            alt="More Power" id="more-power"></a></button>
                                            </div>
                                            <div class="mb-3 fs-3">
                                                <span>Categories:</span>
                                                @php($categories = $profile->categories()->get())
                                                @if ($categories->count() === 0)
                                                    <span class="badge bg-secondary fw-normal">🤷&nbsp;None</span>
                                                @else
                                                    @foreach ($categories as $category)
                                                        <span
                                                            class="badge bg-primary fw-normal">{{ $category->name }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="mb-3 fs-3">
                                                <span>Keywords:</span>
                                                @php($keywords = $profile->keywords()->get())
                                                @if ($keywords->count() === 0)
                                                    <span class="badge bg-secondary fw-normal">🤷&nbsp;None</span>
                                                @else
                                                    @foreach ($keywords as $keyword)
                                                        <span
                                                            class="badge bg-primary fw-normal">{{ $keyword->name }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <!--
                                            Categories
                                            -->
                                        @else
                                            n/a
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Check-In Form -->
                            @if ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button @class([
                                            'accordion-button',
                                            'fs-3',
                                            'collapsed' => !$errors->hasBag('check-in'),
                                        ]) type="button" data-bs-toggle="collapse"
                                            data-bs-target="#checkIn"
                                            aria-expanded="{{ $errors->hasBag('check-in') ? 'true' : 'false' }}"
                                            aria-controls="checkIn" tabindex="3">
                                            Check-In
                                        </button>
                                    </h2>
                                    <div id="checkIn" @class([
                                        'accordion-collapse',
                                        'collapse',
                                        'show' => $errors->hasBag('check-in'),
                                    ])
                                        data-bs-parent="#applicationData">
                                        <div class="accordion-body">
                                            <form method="post" action="{{ route('frontdesk.check-in') }}"
                                                name="check-in" autocomplete="off">
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('waiver_signed', 'check-in') is-invalid @enderror"
                                                        type="checkbox" name="waiver_signed" id="waiver_signed"
                                                        @checked(old('_token') && old('waiver_signed')) tabindex="2">
                                                    <label class="form-check-label" for="waiver_signed">
                                                        waiver signed and received
                                                    </label>
                                                </div>
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('badge_received', 'check-in') is-invalid @enderror"
                                                        type="checkbox" name="badge_received" id="badge_received"
                                                        @checked(old('_token') && old('badge_received')) tabindex="2">
                                                    <label class="form-check-label" for="badge_received">
                                                        badge handed out
                                                    </label>
                                                </div>
                                                <textarea class="form-control my-2 fs-4 w-100 @error('comment', 'check-in') is-invalid @enderror" id="ci_comment"
                                                    name="ci_comment" tabindex="2" placeholder="Additional notes on check-in">{{ old('_token') ? old('ci_comment') : '' }}</textarea>
                                                <button class="form-control btn btn-success my-2 fs-3" type="submit"
                                                    tabindex="2">Perform
                                                    Check-In</button>
                                                <input type="hidden" name="application"
                                                    value="{{ $application->id }}">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Check-Out Form -->
                            @elseif ($application->status === \App\Enums\ApplicationStatus::CheckedIn)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button @class([
                                            'accordion-button',
                                            'fs-3',
                                            'collapsed' => !$errors->hasBag('check-out'),
                                        ]) type="button" data-bs-toggle="collapse"
                                            data-bs-target="#checkOut"
                                            aria-expanded="{{ $errors->hasBag('check-out') ? 'true' : 'false' }}"
                                            aria-controls="checkOut" tabindex="3">
                                            Check-Out
                                        </button>
                                    </h2>
                                    <div id="checkOut" @class([
                                        'accordion-collapse',
                                        'collapse',
                                        'show' => $errors->hasBag('check-out'),
                                    ])
                                        data-bs-parent="#applicationData">
                                        <div class="accordion-body">
                                            <form method="post" action="{{ route('frontdesk.check-out') }}"
                                                name="check-out" autocomplete="off">
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('table_clean', 'check-out') is-invalid @enderror"
                                                        type="checkbox" name="table_clean" id="table_clean"
                                                        @checked(old('_token') && old('table_clean')) tabindex="2">
                                                    <label class="form-check-label" for="table_clean">
                                                        table clean
                                                    </label>
                                                </div>
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('waste_disposed', 'check-out') is-invalid @enderror"
                                                        type="checkbox" name="waste_disposed" id="waste_disposed"
                                                        @checked(old('_token') && old('waste_disposed')) tabindex="2">
                                                    <label class="form-check-label" for="waste_disposed">
                                                        waste disposed of
                                                    </label>
                                                </div>
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('floor_undamaged', 'check-out') is-invalid @enderror"
                                                        type="checkbox" name="floor_undamaged" id="floor_undamaged"
                                                        @checked(old('_token') && old('floor_undamaged')) tabindex="2">
                                                    <label class="form-check-label" for="floor_undamaged">
                                                        floor undamaged
                                                    </label>
                                                </div>
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('materials_removed', 'check-out') is-invalid @enderror"
                                                        type="checkbox" name="materials_removed"
                                                        id="materials_removed" @checked(old('_token') && old('materials_removed'))
                                                        tabindex="2">
                                                    <label class="form-check-label" for="materials_removed">
                                                        all materials (e.g. boxes, merch) removed
                                                    </label>
                                                </div>
                                                <div class="form-check fs-3">
                                                    <input
                                                        class="form-check-input @error('power_strip', 'check-out') is-invalid @enderror"
                                                        type="checkbox" name="power_strip" id="power_strip"
                                                        @checked(old('_token') && old('power_strip')) tabindex="2">
                                                    <label class="form-check-label" for="power_strip">
                                                        power strip in good state or not applicable
                                                    </label>
                                                </div>
                                                <textarea class="form-control my-2 fs-4 w-100 @error('co_comment', 'check-out') is-invalid @enderror" id="co_comment"
                                                    name="co_comment" tabindex="2" placeholder="Additional notes on check-out">{{ old('_token') ? old('co_comment') : '' }}</textarea>
                                                <button class="form-control btn btn-success my-2 fs-3" type="submit"
                                                    tabindex="2">Perform
                                                    Check-Out</button>
                                                <input type="hidden" name="application"
                                                    value="{{ $application->id }}">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="card my-2">
                            <div class="card-header text-center fs-3">
                                Search results for "{{ $search }}"
                            </div>
                            <div class="card-body fs-4">
                                <div class="list-group">
                                    @foreach ($applications as $applicationResult)
                                        <a class="list-group-item list-group-item-action"
                                            href="?type=default&search={{ $applicationResult->user->reg_id }}">
                                            {{ $applicationResult->user->name }}
                                            ({{ $applicationResult->user->reg_id }})
                                            [{{ $applicationResult->parent?->table_number ?? $applicationResult->table_number }}]
                                            @switch($applicationResult->type)
                                                @case(\App\Enums\ApplicationType::Dealer)
                                                    <span
                                                        class="badge text-bg-success mx-2">{{ $applicationResult->type->name }}</span>
                                                @break

                                                @case(\App\Enums\ApplicationType::Share)
                                                    <span
                                                        class="badge text-bg-warning mx-2">{{ $applicationResult->type->name }}</span>
                                                @break

                                                @case(\App\Enums\ApplicationType::Assistant)
                                                    <span
                                                        class="badge text-bg-info mx-2">{{ $applicationResult->type->name }}</span>
                                                @break

                                                @default
                                                    <span class="badge text-bg-danger mx-2">Unknown Type!</span>
                                            @endswitch
                                            @if ($applicationResult->is_afterdark)
                                                <span class="badge text-bg-dark">ADD</span>
                                            @endif
                                            <span
                                                class="badge text-bg-secondary">{{ $applicationResult->status->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Comments Column -->
                <div class="col-md mh-100 overflow-auto">
                    <form method="post" action="{{ route('frontdesk.comment') }}" name="comment"
                        autocomplete="off">
                        <div class="card my-2">
                            <div class="card-header fs-3">
                                Comments
                            </div>
                            <div class="card-body">
                                <textarea class="form-control my-2 fs-4 w-100 @error('comment') is-invalid @enderror" id="comment" name="comment"
                                    @disabled(empty($application)) tabindex="2">{{ old('_token') ? old('comment') : isset($comment) && $comment?->text }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div
                                class="card-footer text-muted d-flex justify-content-between align-items-center text-center">
                                <div>
                                    <input class="form-check-input fs-4" type="checkbox" name="admin_only"
                                        id="admin_only" @checked(old('_token') ? old('admin_only') : isset($comment) && $comment?->admin_only === true) @disabled(empty($application))
                                        tabindex="2">
                                    <label class="form-check-label fs-4" for="admin_only">
                                        admin-only
                                    </label>
                                </div>
                                <div class="flex-fill">
                                    <button class="btn btn-success mx-2 fs-3 w-100" type="submit"
                                        @disabled(empty($application)) tabindex="2">↵</button>
                                </div>
                            </div>
                        </div>
                        @csrf
                        <input type="hidden" name="application" value="{{ $application ? $application->id : '' }}">
                    </form>
                    @if ($application)
                        @foreach ($application->comments()->orderBy('created_at', 'desc')->get() as $comment)
                            @can('view', $comment)
                                <div class="card my-2">
                                    <div class="card-body fs-4" style="white-space: pre-line;">{{ $comment->text }}</div>
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
