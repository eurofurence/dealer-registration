@extends('layouts.app')
@section('title')
    Apply as a {{ $applicationType->value }}
@endsection
@section('content')
    <div class="">
        <h1>Apply as a {{ $applicationType->value }}</h1>
        <x-application.intro :applicationType="$applicationType"></x-application.intro>

        @if ($invitingApplication)
            <div class="alert alert-info text-center">
                <div class="mx-auto">
                    <h3>Confirm Invitation</h3>
                    <p>
                        To accept your invitation from <em>{{ $invitingApplication->getFullName() }}</em>
                        and become part of their dealership as <em>{{ ucfirst($applicationType->value) }}</em>, please
                        <strong>review the data below and click on "Submit your application"</strong>.
                    </p>
                </div>
            </div>
        @endif

        @if ($errors->all())
            <div class="alert alert-danger text-center fw-bold">There were some issues saving your application, please see
                below.</div>
        @endif

        <form class="needs-validation" method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data">
            <div class="accordion" id="application-form">
                @include('forms.application')
                @if ($applicationType === \App\Enums\ApplicationType::Dealer || $applicationType === \App\Enums\ApplicationType::Share)
                    @include('forms.profile')
                @endif
            </div>
            @csrf
            <button class="w-100 btn btn-primary btn-lg mt-4" type="submit">Submit your application</button>
            <input type="hidden" name="confirmation" value="{{ $confirmation }}">
        </form>
    </div>
@endsection
