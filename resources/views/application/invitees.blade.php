@extends('layouts.app')
@section('content')
    <div class="">
        <div class="col-md-6 mx-auto">
            <h1 class="text-center">Manage shares and assistants</h1>
            <p class="text-center lead">You can give invite codes to users that you wish to either setup a share with or invite as an assistant.<br>
                <br>Please note, depending on the space size you selected during registration, the amount of people is limited.
            </p>
        </div>
        @if(Session::exists('removal-successful'))
            <div class="alert alert-success text-center fw-bold">The user has been removed.</div>
        @endif

        <div class="mx-auto text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
        </div>
        <div class="row mb-5">
            <div class="col-md-3 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center display-6">Seats ({{ $currentSeats }}/{{ $maxSeats }})</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title"><h5>Share your space with other dealers</h5></div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if($currentSeats < $maxSeats)
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.regenerate-keys') }}">
                                    @csrf
                                    <label for="invite-code-assistants">Invite code for shares</label>
                                    <button class="btn btn-sm btn-link" type="submit">Regenerate Keys</button>
                                    <input id="invite-code-assistants" disabled class="form-control"
                                           value="{{ $application->invite_code_shares }}">
                                    <span
                                        class="form-text">Ask your share to go to dealers.eurofurence.org and click on Join, after that they need to enter the above code.</span>
                                </form>
                            </li>
                        @endif
                        @foreach($shares as $share)
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.destroy') }}">
                                    @method('DELETE')
                                    @csrf
                                    <input type="hidden" name="invitee_id" value="{{ $share->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger d-inline">X</button>
                                    {{ $share->display_name ?? $share->user->name }}
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title"><h5>Invite assistants to your space</h5></div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if($currentSeats < $maxSeats)
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.regenerate-keys') }}">
                                    @csrf
                                    <label for="invite-code-assistants">Invite code for assistants</label>
                                    <button class="btn btn-sm btn-link" type="submit">Regenerate Keys</button>
                                    <input id="invite-code-assistants" disabled class="form-control"
                                           value="{{ $application->invite_code_assistants }}">
                                    <span
                                        class="form-text">Ask your assistant to go to dealers.eurofurence.org and click on Join, after that they need to enter the above code.</span>
                                </form>
                            </li>
                        @endif
                        @foreach($assistants as $share)
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('applications.invitees.destroy') }}">
                                    @method('DELETE')
                                    @csrf
                                    <input type="hidden" name="invitee_id" value="{{ $share->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger d-inline">X</button>
                                    {{ $share->display_name ?? $share->user->name }}
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
