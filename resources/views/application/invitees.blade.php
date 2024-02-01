@extends('layouts.app')
@section('title')
    Shares and Assistants
@endsection
@section('content')
    <style>
        .dd-table-button {
            width: 5ex;
            height: 5ex;
            font-weight: bold;
        }

        .dd-table-button.border-danger {
            border-width: 3px
        }
    </style>
    <div class="">
        <div class="col-md-6 mx-auto">
            <h1 class="text-center">Manage Shares and Assistants</h1>
            <p class="text-center lead">You can give invite codes to users that you wish to either setup a share with or
                invite as an assistant. Please note that changes to shares are only possible during the registration
                period.<br>
                <br>Please note, depending on the space size you selected during registration, the amount of people is
                limited.
            </p>
        </div>
        @if (Session::exists('removal-successful'))
            <div class="alert alert-success text-center fw-bold">The user has been removed.</div>
        @endif

        @if ($seats['free'] >= 0)
            <div class="mx-auto text-center mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
            </div>
        @endif
        <div class="col-md-6 mx-auto">
            <h3 class="text-center">Seats in your Dealership</h3>
            <div class="mx-auto text-center mb-1">
                @for ($i = 0; $i < $seats['dealers']; $i++)
                    <button
                        class="btn btn-sm btn-primary dd-table-button @if ($i >= $seats['table']) border-danger text-danger @endif"
                        type="button" title="Dealer">D</button>
                @endfor
                @for ($i = 0; $i < $seats['free']; $i++)
                    <button class="btn btn-sm btn-secondary dd-table-button" type="button" title="Free">F</button>
                @endfor
                @if (!is_null($seats['additional']))
                    <button class="btn btn-sm btn-dark dd-table-button" type="button"
                        title="Free ({{ ucfirst($seats['additional']) }})">F{{ ucfirst(substr($seats['additional'],0,1)) }}</button>
                @endif
                @for ($i = 0; $i < $seats['assistants']; $i++)
                    <button
                        class="btn btn-sm bg-info dd-table-button @if ($i >= max($seats['table'] - $seats['dealers'], 1)) border-danger text-danger @endif"
                        type="button" title="Assistant">A</button>
                @endfor
            </div>
            <div class="mx-auto text-center mb-4">
                Legend:
                <span class="badge text-bg-primary">Dealer</span>
                <span class="badge text-bg-secondary">Free</span>
                @if (!is_null($seats['additional']))
                    <span class="badge text-bg-dark">Free ({{ ucfirst($seats['additional']) }})</span>
                @endif
                <span class="badge text-bg-info">Assistant</span>
            </div>
        </div>
        @if ($seats['free'] < 0)
            <div class="alert alert-danger text-center fw-bold">
                You have too many people in your dealership for the number of
                seats available for your table size.<br />
                Please remove excess shares or assistants.
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">

                <div class="card mb-2 @if ($seats['free'] < 0) bg-danger-subtle @endif">
                    <div class="card-body">
                        <div class="card-title h4 mb-0">
                            Share your space with other dealers
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if (
                            ($seats['free'] > 0 || $seats['additional'] === 'dealer') &&
                                Carbon\Carbon::parse(config('con.reg_end_date'))->isFuture())
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.codes') }}">
                                    @csrf
                                    <label for="invite-code-shares">Invite code for shares</label>
                                    <button class="btn btn-sm btn-link" type="submit" name="action"
                                        value="regenerate">Regenerate</button>
                                    <button class="btn btn-sm btn-link link-danger" type="submit" name="action"
                                        value="clear">Disable</button>
                                    <input id="invite-code-shares" readonly class="form-control"
                                        value="{{ $application->invite_code_shares }}" onclick="this.select()"
                                        placeholder="— click Regenerate for new code —">
                                    <input type="hidden" name="type" value="shares">
                                    <span class="form-text">
                                        Ask your share to go to <a href="{{ url('') }}">{{ url('') }}</a> and
                                        enter the invitation code above
                                        to join you.
                                    </span>
                                </form>
                            </li>
                        @endif
                        @foreach ($shares as $share)
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.destroy') }}">
                                    @method('DELETE')
                                    @csrf
                                    <input type="hidden" name="invitee_id" value="{{ $share->id }}">
                                    @if (
                                        $application->status === \App\Enums\ApplicationStatus::Open &&
                                            Carbon\Carbon::parse(config('con.reg_end_date'))->isFuture())
                                        <button type="submit" class="btn btn-sm btn-danger d-inline">X</button>
                                    @endif
                                    {{ $share->getFullName() }}
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-2 @if ($seats['free'] < 0 && $seats['additional'] !== 'assistant') bg-danger-subtle @endif">
                    <div class="card-body">
                        <div class="card-title h4 mb-0">
                            Invite assistants to your space
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if (
                            ($seats['free'] > 0 || $seats['additional'] === 'assistant') &&
                                Carbon\Carbon::parse(config('con.assistant_end_date'))->isFuture())
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.codes') }}">
                                    @csrf
                                    <label for="invite-code-assistants">Invite code for assistants</label>
                                    <button class="btn btn-sm btn-link" type="submit" name="action"
                                        value="regenerate">Regenerate</button>
                                    <button class="btn btn-sm btn-link link-danger" type="submit" name="action"
                                        value="clear">Disable</button>
                                    <input id="invite-code-assistants" readonly class="form-control"
                                        value="{{ $application->invite_code_assistants }}" onclick="this.select()"
                                        placeholder="— click Regenerate for new code —">
                                    <input type="hidden" name="type" value="assistants">
                                    <span class="form-text">Ask your assistant to go to <a
                                            href="{{ url('') }}">{{ url('') }}</a> and enter
                                        the invitation code above to join you.</span>
                                </form>
                            </li>
                        @endif
                        @foreach ($assistants as $assistant)
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.destroy') }}">
                                    @method('DELETE')
                                    @csrf
                                    <input type="hidden" name="invitee_id" value="{{ $assistant->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger d-inline">X</button>
                                    {{ $assistant->getFullName() }}
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
