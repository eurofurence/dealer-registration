@extends('layouts.app')
@section('title')
    Update your {{ $applicationType->value }} application
@endsection
@section('content')
    <div class="">
        <h1>Update your application</h1>
        @if (now() < config('ef.reg_end_date'))
            <p>Please fill out this application form to apply for your participation in the Dealers' Den. You
                may edit your application by coming back here, until the application deadline on
                <b>{{ config('ef.reg_end_date',now())->format('d.m.Y H:i') }}</b>.
                Please read the help texts carefully, and enter your information to the best of your ability.</p>
            <p>
                As always, you can tell us what you'd like your table to be called, and
                with whom you're joining forces. The fields you need to fill out for this
                are "Display Name" and "Must-Have Neighbor". Here's a little picture that
                explains what you can achieve with these options:
            </p>
        @else            
            <p>
                We'd like to ask you to provide some details about you and your
                merchandise for our convention guests which will be displayed in the EF app. 
            </p>    
            <p>
                It would be great if you can provide some short descriptions about yourself
                and about what people can expect to find at your table. There's also room for
                a small avatar picture, a profile picture that's shown on a separate detail page,
                and, optionally, a sample piece of artwork.
            </p>        
        @endif

        <img class="mx-auto d-block mb-5" data-bs-toggle="modal" data-bs-target="#exampleModal"
             src="{{ Vite::asset('resources/assets/naming.small.jpg') }}" alt="">

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img class="mx-auto d-block w-100" src="{{ Vite::asset('resources/assets/naming.large.jpg') }}"
                             alt="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

            <div class="mx-auto text-center mb-4">
                @if($application->type === \App\Enums\ApplicationType::Dealer)
                <a href="{{ route('applications.invitees.view') }}" class="btn btn-lg btn-outline-primary mb-2">Assistants & Shares</a>
                @endif
                <a href="{{ route('applications.delete') }}" class="btn btn-lg btn-outline-danger mb-2">
                    @if($application->type === \App\Enums\ApplicationType::Dealer)
                        Cancel Registration
                    @else
                        Leave Dealership
                    @endif
                </a>
            </div>

        @if($errors->all())
            <div class="alert alert-danger text-center fw-bold">There were some issues saving your application, please see below.</div>
        @endif

        @if(Session::exists('save-successful'))
            <div class="alert alert-success text-center fw-bold">Your data has been successfully saved.</div>
        @endif

        <form class="needs-validation" method="POST" action="{{ route('applications.update') }}" enctype="multipart/form-data">
            @method('PUT')
            @include('forms.application')
            @if ($applicationType === \App\Enums\ApplicationType::Dealer || $applicationType === \App\Enums\ApplicationType::Share)
                @include('forms.profile')
            @endif  
            @csrf
            <button class="w-100 btn btn-primary btn-lg mt-4" type="submit">Update your application</button>            
        </form>
    </div>
@endsection
