@extends('layouts.app')
@section('content')
    <div class="">
        <div class="mx-auto">
            <h1 class="text-center">Join an existing Dealership</h1>
            <p class="text-center lead">
                You were invited by
                <strong>{{ $invitingApplication->getFullName() }}</strong>
                to become part of their dealership as <strong>{{ ucfirst($invitationType->value) }}</strong>!
            </p>
            @if ($invitationType === \App\Enums\ApplicationType::Share)
                <p class="text-center lead">
                    Accepting this invitation will make you part of their joint dealership, sharing their dealership space
                    with you as an authorized dealer without dealership space once your joint application is accepted. For
                    additional information, please read the <a href="{{ config('con.dealers_tos_url') }}"
                        target="_blank">Dealers’ Den Rules & Information</a>.
                </p>
            @elseif ($invitationType === \App\Enums\ApplicationType::Assistant)
                <p class="text-center lead">
                    Accepting this invitation allows you to support them, while holding the same access and sales privileges
                    they have. For additional information, please read the <a href="{{ config('con.dealers_tos_url') }}"
                        target="_blank">Dealers’ Den Rules & Information</a>.
                </p>
            @endif
            @if ($application && $application->type === \App\Enums\ApplicationType::Dealer && $application->isActive())
                <p class="text-center lead alert alert-warning">
                    You currently have an active application as <strong>Dealer</strong>.<br>
                    Accepting this invitation means you will give up your previous application as a dealer and join another
                    dealership as <strong>{{ ucfirst($invitationType->value) }}</strong>.
                </p>
            @endif
        </div>

        <div class="card">
            <form action="{{ route('invitation.confirm') }}" method="post">
                <div class="card-body text-center">
                    <h5 class="card-title display-6">Accept Invitation</h5>
                    <p class="card-text lead">
                        Do you want to join
                        <strong>{{ $invitingApplication->getFullName() }}</strong>
                        as their <strong>{{ ucfirst($invitationType->value) }}</strong>?
                    </p>
                    <p class="card-text text-center">
                        <button class="btn btn-primary" type="submit">Yes, accept invitation!
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-danger">No, return to dashboard.</a>
                    </p>
                </div>
        </div>
        <input type="hidden" name="code" value="{{ $code }}">
        <input type="hidden" name="confirmation" value="{{ $confirmation }}">
        @csrf
        </form>
    </div>
    </div>
@endsection
