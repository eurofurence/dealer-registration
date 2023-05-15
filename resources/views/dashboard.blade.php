@extends('layouts.app')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="px-4 py-5 my-5 text-center">
        <h1 class="display-5 fw-bold">Register</h1>
        <div class="col-lg-6 mx-auto">
            @if (Carbon\Carbon::parse(config('ef.reg_end_date'))->isFuture())
                <p class="lead mb-4">
                    Application for a Dealership is open until
                    <b>{{ Carbon\Carbon::parse(config('ef.reg_end_date'))->format('d.m.Y H:i') }}</b>.
                </p>
            @else
                <p class="lead mb-4">The registration period has ended. You can still update your profile data which will be
                    displayed in the EF app.</p>
            @endif
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a target="_blank" href="{{ config('ef.dealers_tos_url') }}" class="text-secondary small">Rules and
                    Information</a>
            </div>
        </div>
    </div>
    @if (isset($application))
        @if (
            $application->type === \App\Enums\ApplicationType::Dealer &&
                ($application->getStatus() === \App\Enums\ApplicationStatus::Open || (($application->status === \App\Enums\ApplicationStatus::TableOffered || $application->status === \App\Enums\ApplicationStatus::Waiting) && !$application->is_notified)))
            <div class="alert alert-info text-center">
                <h3>Your registration as a dealer is currently being reviewed.</h3>
                <span>Please wait for our team to process your application. You will be notified via email if your
                    application status changes.</span>
            </div>
        @endif

        @if ($application->type === \App\Enums\ApplicationType::Dealer && $application->status === \App\Enums\ApplicationStatus::TableOffered && $application->is_notified)
            <div class="alert alert-info text-center">
                <h3>Congratulations!</h3>
                <p>Your registration as a dealer was accepted! Please review and accept the table you were offered.</p>
                <a href="{{ route('table.confirm') }}" class="btn btn-lg btn-primary">Review Offered Table</a>
            </div>
        @endif

        @if ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
            <div class="alert alert-info text-center">
                <h3>See you at Eurofurence!</h3>
                <p>Your table in this year's Dealers' Den is: <strong>{{$application->table_number}}</strong></p>
            </div>
        @endif

        @if ($application->status === \App\Enums\ApplicationStatus::CheckedIn)
            <div class="alert alert-info text-center">
                <h3>Welcome to Eurofurence!</h3>
                <p>Your table in this year's Dealers' Den is: <strong>{{$application->table_number}}</strong></p>
            </div>
        @endif

        @if (
            $application->type === \App\Enums\ApplicationType::Dealer &&
                $application->getStatus() === \App\Enums\ApplicationStatus::Canceled)
            <div class="alert alert-danger text-center fw-bold">Your application has been canceled.</div>
        @endif
    @endif

    <div class="row">
        @if (isset($application) && $application->isActive())
            <div class="col-md-6">
                <div class="card mb-2">
                    <div class="card-body text-center">
                        <h5 class="card-title display-6">Manage your Registration</h5>
                        <p class="card-text lead">You may update your registration, update it or cancel your registration
                            as long as the registration has not closed yet. You can also invite other dealers and assistants
                            to
                            your slot.</p>
                        <a href="{{ route('applications.edit') }}" class="btn btn-lg btn-primary">Manage your
                            Registration</a>
                        <div class="mb-3"></div>
                        @if ($application->type === \App\Enums\ApplicationType::Dealer)
                            <a href="{{ route('applications.invitees.view') }}"
                                class="btn btn-sm btn-outline-primary">Assistants & Shares</a>
                        @endif
                        @if ($application->status !== \App\Enums\ApplicationStatus::TableAccepted && $application->status !== \App\Enums\ApplicationStatus::CheckedIn)
                        <a href="{{ route('applications.delete') }}" class="btn btn-sm btn-outline-danger">
                            @if ($application->type === \App\Enums\ApplicationType::Dealer)
                                Cancel Registration
                            @else
                                Leave Dealership
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif (Carbon\Carbon::parse(config('ef.reg_end_date'))->isFuture())
            <div class="col-md-6">
                <div class="card mb-2">
                    <div class="card-body text-center">
                        <h5 class="card-title display-6">Apply for a Dealership</h5>
                        <p class="card-text lead">As a Dealers' Den dealership owner, you are responsible for managing your
                            space. While you may choose to share your table with other dealers if space permits, you will be
                            the primary point of contact for your business.</p>
                        <a href="{{ route('applications.create') }}" class="btn btn-lg btn-primary">Submit your
                            Dealership application</a>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-6 mx-auto">
            @if (Carbon\Carbon::parse(config('ef.reg_end_date'))->isFuture())
                <div class="card mb-2">
                    <div class="card-body text-center">
                        <h5 class="card-title display-6">Join an existing Dealership</h5>
                        <p class="card-text lead">You have been invited by an existing dealership to share their space at
                            the Dealers' Den.</p>
                        <a href="{{ route('join') }}" class="btn btn-lg btn-outline-primary">I have an invitation code</a>
                    </div>
                </div>
            @endif
            <div class="card mb-2">
                <div class="card-body text-center">
                    <h5 class="card-title display-6">Become a Dealer Assistant</h5>
                    <p class="card-text lead">You have been invited to support an existing Dealership as a Dealer Assistant.
                    </p>
                    <a href="{{ route('join') }}" class="btn btn-lg btn-outline-primary">I have an invitation code</a>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
