@extends('layouts.app')
@section('title')
    Shares and Assistants
@endsection
@section('content')
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

        @if ($assistants_active_count <= $assistants_count && $shares_active_count <= $shares_count)
            <div class="mx-auto text-center mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                @if ($shares_active_count > $shares_count)
                    <div class="alert alert-danger fw-bold">You have too many dealers for your table size. <br>Please remove
                        dealers.</div>
                @endif

                <div class="card mb-2 @if ($shares_active_count > $shares_count) bg-danger-subtle @endif">
                    <div class="card-body">
                        <div class="card-title h5 mb-0"><span
                                class="badge bg-secondary">{{ $shares_active_count }}/{{ $shares_count }}</span> Share your
                            space with other dealers</div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if ($shares_active_count < $shares_count && Carbon\Carbon::parse(config('con.reg_end_date'))->isFuture())
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
                                        Ask your share to go to dealers.eurofurence.org and enter the invitation code above
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
                @if ($assistants_active_count > $assistants_count)
                    <div class="alert alert-danger fw-bold">You have too many assistants for your table size. <br>Please
                        remove assistants.</div>
                @endif
                <div class="card mb-2 @if ($assistants_active_count > $assistants_count) bg-danger-subtle @endif">
                    <div class="card-body">
                        <div class="card-title h5 mb-0"><span
                                class="badge bg-secondary">{{ $assistants_active_count }}/{{ $assistants_count }}</span>
                            Invite assistants to your space</div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if ($assistants_active_count < $assistants_count && Carbon\Carbon::parse(config('con.assistant_end_date'))->isFuture())
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
                                    <span class="form-text">Ask your assistant to go to dealers.eurofurence.org and enter
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
                                    $assistant->user->name
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
