@extends('layouts.app')
@section('title')
    Apply as a {{ $applicationType->value }}
@endsection
@section('content')
    <div class="">
        <h1>Apply as a {{ $applicationType->value }}</h1>
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

        @if ($errors->all())
            <div class="alert alert-danger text-center fw-bold">There were some issues saving your application, please see
                below.</div>
        @endif

        <form class="needs-validation" method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data">
            @include('forms.application')
            @if ($applicationType === \App\Enums\ApplicationType::Dealer || $applicationType === \App\Enums\ApplicationType::Share)
                @include('forms.profile')
            @endif
            @csrf
            <button class="w-100 btn btn-primary btn-lg mt-4" type="submit">Submit your application</button>
        </form>
    </div>
@endsection
