@extends('layouts.app')
@section('title')
    Update your {{ $applicationType->value }} application
@endsection
@section('content')
    <div class="">
        <h1>Update your application</h1>
        @if (Carbon\Carbon::parse(config('con.reg_end_date'))->isFuture())
            <p>Please fill out this application form to apply for a Dealership at the Dealers’ Den. You may edit your
                application at any time before the deadline of
                <b>{{ Carbon\Carbon::parse(config('con.reg_end_date'))->format('d.m.Y H:i') }}</b>.
                Please read the help texts carefully, and enter your information to the best of your ability.
            </p>
            <p>
                The Dealers’ Den Management will review your submission after the registration period has ended. Status
                emails about approval, denial, or being put on the waiting list will be sent after the review. Upon
                approval, your Eurofurence registration will be updated with a dealership package. The package will show up
                as an extra amount due in your registration. Payment is handled through the Eurofurence Registration
                website.
            </p>
        @else
            <p>The registration period has ended. You can still update your profile data which will be displayed in the EF
                app.</p>
        @endif

        <div class="mx-auto text-center mb-4">
            @if ($application->type === \App\Enums\ApplicationType::Dealer)
                <a href="{{ route('applications.invitees.view') }}" class="btn btn-lg btn-outline-primary mb-2">Assistants &
                    Shares</a>
            @endif
            <a href="{{ route('applications.delete') }}" class="btn btn-lg btn-outline-danger mb-2">
                @if ($application->type === \App\Enums\ApplicationType::Dealer)
                    Cancel Registration
                @else
                    Leave Dealership
                @endif
            </a>
        </div>

        @if ($invitingApplication)
            <div class="alert alert-info text-center">
                <div class="mx-auto">
                    <h3>Confirm Invitation</h3>
                    <p>
                        To accept your invitation from <em>{{ $invitingApplication->getFullName() }}</em>
                        and become part of their dealership as <em>{{ ucfirst($applicationType->value) }}</em>, please
                        <strong>review the data below and click on "Update your application"</strong>.
                    </p>
                </div>
            </div>
        @endif

        @if ($errors->all())
            <div class="alert alert-danger text-center fw-bold">There were some issues saving your application, please see
                below.</div>
        @endif

        @if (Session::exists('save-successful'))
            <div class="alert alert-success text-center fw-bold">Your data has been saved successfully.</div>
        @endif

        <form class="needs-validation" method="POST" action="{{ route('applications.update') }}"
            enctype="multipart/form-data">
            @method('PUT')
            @include('forms.application')
            @if ($applicationType === \App\Enums\ApplicationType::Dealer || $applicationType === \App\Enums\ApplicationType::Share)
                @include('forms.profile')
            @endif
            @csrf
            <button class="w-100 btn btn-primary btn-lg mt-4" type="submit">Update your application</button>
            <input type="hidden" name="confirmation" value="{{ $confirmation }}">
        </form>
    </div>
@endsection
