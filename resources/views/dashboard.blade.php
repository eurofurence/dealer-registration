@extends('layouts.app')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="px-4 py-3 my-3 text-center">
        <h1 class="display-5 fw-bold">Register</h1>
        <div class="col-lg-6 mx-auto">
            @if (Carbon\Carbon::parse(config('convention.reg_end_date'))->isFuture())
                <p class="lead mb-4">
                    Application for dealerships is open until
                    <b>{{ Carbon\Carbon::parse(config('convention.reg_end_date'))->format('d.m.Y H:i') }}</b>.
                </p>
                <p class="lead mb-4">
                    Please note that a <strong>valid registration for Eurofurence</strong> by that date is required to apply
                    for the Dealers’ Den.<br />
                    Applications for dealerships will only be taken into consideration if <strong>all members</strong> have
                    a valid registration when the application phase ends.
                </p>
                @if (!$application?->isActive() ?? false)
                    <p class="lead mb-4">
                        @if ($registration)
                            You are <strong>registered</strong> for this year's Eurofurence with <em>badge number
                                {{ $registration['id'] }}</em> and your registration status is
                            <em>{{ $registration['status'] }}</em>.
                        @else
                            You currently do not seem to be registered for this year's Eurofurence.
                        @endif
                    </p>
                @endif
            @else
                <p class="lead mb-4">The registration period for dealerships has ended. You can still update your profile
                    data which will be displayed in the EF app.</p>
            @endif

            @if (Carbon\Carbon::parse(config('convention.assistant_end_date'))->isFuture())
                <p class="lead mb-4">
                    Registration for assistants remains open until
                    <b>{{ Carbon\Carbon::parse(config('convention.assistant_end_date'))->format('d.m.Y H:i') }}</b>.
                </p>
            @endif
            @if ($application->type === \App\Enums\ApplicationType::Dealer && Carbon\Carbon::parse(config('convention.physical_chairs_end_date'))->isFuture())
                <p class="lead mb-4">
                    <span class="badge text-bg-warning">New:</span> Please adjust the number of physical chairs you need for your dealership until
                    <b>{{ Carbon\Carbon::parse(config('convention.physical_chairs_end_date'))->format('d.m.Y H:i') }}</b>.<br>
                    <a href="{{ route('applications.invitees.view') }}" class="btn btn btn-outline-primary mt-2">
                        Change number of physical chairs
                    </a>
                </p>
            @endif
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a target="_blank" href="{{ config('convention.dealers_tos_url') }}" class="btn btn-outline-primary btn-lg">Rules and
                    Information</a>
            </div>
        </div>
    </div>
    @if (isset($application))
        @if (
            $application->type === \App\Enums\ApplicationType::Dealer &&
                $application->getStatus() === \App\Enums\ApplicationStatus::Canceled)
            <div class="alert alert-danger text-center fw-bold">Your application has been canceled.</div>
        @else
            <div class="alert alert-info text-center">
                <div class="w-50 mx-auto">
                    @if (
                        $application->getStatus() === \App\Enums\ApplicationStatus::Open ||
                            $application->getStatus() === \App\Enums\ApplicationStatus::TableAssigned)
                        @if ($application->type === \App\Enums\ApplicationType::Dealer)
                            <h3>Application in Review</h3>
                            <p>Your registration as a dealer is currently being reviewed.</p>
                            <p>Please wait for our team to process your application. You will be notified via email if your
                                application status changes.</p>
                        @else
                            <h3>Application in Review</h3>
                            <p>The registration of the dealership you are part of is currently being reviewed.</p>
                            <p>Please wait for our team to process the application. The main account of your dealership will
                                be
                                notified via email if the application status changes.</p>
                        @endif
                    @elseif ($application->getStatus() === \App\Enums\ApplicationStatus::Waiting)
                        @if ($application->type === \App\Enums\ApplicationType::Dealer)
                            <h3>Application on Waiting List</h3>
                            <p>Your registration as a dealer is currently on the waiting list.</p>
                            <p>Please be patient, you will be notified via email if your
                                application status changes.</p>
                        @else
                            <h3>Application on Waiting List</h3>
                            <p>The registration of the dealership you are part of is currently on the waiting list.</p>
                            <p>Please be patient, the main account of your dealership will be
                                notified via email if the application status changes.</p>
                        @endif
                    @elseif ($application->status === \App\Enums\ApplicationStatus::TableOffered)
                        @if ($application->type === \App\Enums\ApplicationType::Dealer)
                            <h3>Congratulations!</h3>
                            <p>Your registration as a dealer was accepted! Please review and accept the table you were
                                offered.
                            </p>
                            <p>
                                <a href="{{ route('table.confirm') }}" class="btn btn-lg btn-primary">
                                    Review Offered Table
                                </a>
                            </p>
                        @else
                            <h3>Congratulations!</h3>
                            <p>The application of the dealership you are part of was accepted! The main account of your
                                dealership has been informed via email and needs to review and accept the assigned table.
                            </p>
                            <p>For any questions about the table your dealership was assigned, please contact the person who
                                initated the application.</p>
                        @endif
                    @elseif ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
                        <h3>See you at Eurofurence!</h3>
                        <h5>Your table in this year’s Dealers’ Den will be:
                            <strong>{{ $application->table_number }}</strong>
                        </h5>
                    @elseif ($application->status === \App\Enums\ApplicationStatus::CheckedIn)
                        <h3>Welcome to Eurofurence!</h3>
                        <h5>Your table in this year’s Dealers’ Den is: <strong>{{ $application->table_number }}</strong>
                        </h5>
                    @endif
                    @switch($registration['status'] ?? '')
                        @case('new')
                        @case('approved')

                        @case('partially paid')
                            <p>Our records show that your EF registration status is <em>{{ $registration['status'] }}</em>. Please
                                check and make sure to settle all outstanding dues in a timely manner to ensure everything runs
                                smoothly when you arrive.</p>
                        @break

                        @case('paid')
                            <p>According to our records, your EF registration status is <em>{{ $registration['status'] }}</em>,
                                and you are all set and ready to go!</p>
                        @break

                        @case('cancelled')
                            <p class="alert alert-danger text-center">
                                Going by our records, it seems your EF registration status is
                                <em>{{ $registration['status'] }}</em>.
                                Please contact us at dealers@eurofurence.org if you are not planning on attending Eurofurence
                                this year after all!
                            </p>
                        @break

                        @default
                            <p class="alert alert-danger text-center">
                                <strong>We were unable to find an EF registration for your account!</strong><br>
                                @if (Carbon\Carbon::parse(config('convention.reg_end_date'))->isFuture())
                                    @if ($application->type === \App\Enums\ApplicationType::Assistant)
                                        Please make sure to <em>register and pay for your for this year's Eurofurence before
                                            {{ Carbon\Carbon::parse(config('convention.assistant_end_date'))->format('d.m.Y H:i') }}</em>,
                                        if you wish to be an assistant at the Dealers' Den!
                                    @else
                                        Please make sure to <em>register for this year's Eurofurence before
                                            {{ Carbon\Carbon::parse(config('convention.reg_end_date'))->format('d.m.Y H:i') }}</em>,
                                        if you wish for your application to be taken into consideration for the Dealers' Den!
                                    @endif
                                @else
                                    Please contact us at dealers@eurofurence.org if you are not planning on attending Eurofurence
                                    this year after all!
                                @endif
                            </p>
                    @endswitch
                </div>
            </div>
        @endif
    @endif

    <div class="row">
        @if (isset($application) && $application->isActive())
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center display-6">Profile Completion Progress</h5>
                        <p>
                            <a href="{{ route('applications.edit') }}#profile" class="btn btn-md  float-end btn-primary mt-2">Edit your Profile</a>
                            Please try to fill out as much of your profile as possible!
                            Click on the progress bar to show more details and help us providing our attendees with all the information they need.
                        </p>
                        <x-application.completion-progress :application="$application" />

                    </div>
                </div>
            </div>
            <div class="col-lg-6 mx-auto d-flex">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title display-6">Manage your Registration</h5>
                        @if ($application->type)
                            <p>
                                as
                                <span
                                    class="badge bg-primary">{{ \Illuminate\Support\Str::ucfirst($application->type->value) }}</span>
                                @if ($application->parent)
                                    of <span
                                        class="badge bg-secondary">{{ $application->parent()->first()->getFullName() }}</span>
                                @endif
                            </p>
                        @endif
                        <p class="card-text lead">
                            @switch($application->type)
                                @case(\App\Enums\ApplicationType::Dealer)
                                    You may update your registration as dealer and invite other dealers while registration is still
                                    open and invite assistants until assistant registration closes. Your profile can be updated any
                                    time and cancelling your registration is possible until you have accepted your table offer.
                                @break

                                @case(\App\Enums\ApplicationType::Share)
                                    You may update your registration as share while registration is still open. Your profile can be
                                    updated any time and cancelling your registration is possible until your dealership has accepted
                                    its table offer.
                                @break

                                @case(\App\Enums\ApplicationType::Assistant)
                                    You may cancel your registration as assistant until the assistant registration period is over.
                                @break

                                @default
                                    <span class="badge text-bg-danger mx-2">Please report this as an error.</span>
                            @endswitch
                        </p>
                        <a href="{{ route('applications.edit') }}" class="btn btn-lg btn-primary mt-2">Manage your
                            Registration</a>
                        <div class="mb-3"></div>
                        @if (
                            $application->status !== \App\Enums\ApplicationStatus::TableAccepted &&
                                $application->status !== \App\Enums\ApplicationStatus::CheckedIn)
                            <a href="{{ route('applications.delete') }}" class="btn btn btn-outline-danger mt-2">
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
            @if ($application->type === \App\Enums\ApplicationType::Dealer)
                <div class="col-lg-6 mx-auto d-flex">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title display-6">Seats in your Dealership</h5>
                            <p class="card-text lead">
                                You may invite other dealers while registration is still open and invite assistants until
                                assistant registration closes.
                            </p>
                            <x-dashboard.dealership-seats-overview :seats="$application->getSeats()" />
                            <a href="{{ route('applications.invitees.view') }}" class="btn btn-lg btn-primary">
                                Invite or Manage <span class="text-nowrap">Assistants & Shares</span>
                            </a>
                            @if(Carbon\Carbon::parse(config('convention.physical_chairs_end_date'))->isFuture())
                                <div class="mb-3"></div>
                                <a href="{{ route('applications.invitees.view') }}" class="btn btn btn-outline-primary mt-2">
                                    <span class="badge text-bg-warning me-2">New:</span> Change number of physical chairs
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @elseif (Carbon\Carbon::parse(config('convention.reg_end_date'))->isFuture())
            <div class="col-lg-6 d-flex">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title display-6">Apply for a Dealership</h5>
                        <p class="card-text lead">As a Dealers’ Den dealership owner, you are responsible for managing your
                            space. While you may choose to share your table with other dealers if space permits, you will be
                            the primary point of contact for your business.</p>
                        <a href="{{ route('applications.create') }}" class="btn btn-lg btn-primary">Submit your
                            Dealership application</a>
                    </div>
                </div>
            </div>
        @endif
        @if (Carbon\Carbon::parse(config('convention.reg_end_date'))->isFuture() ||
                Carbon\Carbon::parse(config('convention.assistant_end_date'))->isFuture())
            <div class="col-lg-6 mx-auto d-flex">
                <div class="card mb-4">
                    <form action="{{ route('invitation.join') }}" method="post">
                        <div class="card-body text-center">
                            <h5 class="card-title display-6">Join an existing Dealership</h5>
                            <p class="card-text lead">
                                You have been invited by an existing dealership to
                                @if (Carbon\Carbon::parse(config('convention.reg_end_date'))->isFuture())
                                    share their space or
                                @endif
                                assist them at the Dealers’ Den?<br>
                                Then enter the invite code they provided you with below!
                            </p>
                            <div class="input-group input-group-lg has-validation">
                                @if (isset($application) && $application->isActive())
                                    <input type="text" disabled class="form-control" placeholder="Invite Code">
                                    <button class="btn btn-secondary" disabled type="submit">Submit</button>
                                    <p class="text-danger mt-2">
                                        You cannot join another dealership while you still have an active, uncanceled
                                        application.
                                    </p>
                                @else
                                    <input type="text" name="code"
                                        class="form-control @error('code') is-invalid @enderror" placeholder="Invite Code">
                                    <button class="btn btn-primary" type="submit">Submit
                                    </button>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <span></span>
                                    @enderror
                                @endif
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
