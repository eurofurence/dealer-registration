@extends('layouts.app')
@section('title')
    Apply as a {{ $applicationType->value }}
@endsection
@section('content')
    <div class="">
        <h1>Apply as a {{ $applicationType->value }}</h1>
        <p>Please fill out this application form to apply for your participation in the Dealers' Den. You
            may edit your application by coming back here, until the application deadline on
            <b>{{ config('dates.reg_end_date',now())->format('d.m.Y H:i') }}</b>.
            Please read the help texts carefully, and enter your information to the best of your ability.</p>
        <p>
            As always, you can tell us what you'd like your table to be called, and
            with whom you're joining forces. The fields you need to fill out for this
            are "Display Name" and "Must-Have Neighbor". Here's a little picture that
            explains what you can achieve with these options:
        </p>

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

        @if($errors->all())
            <div class="alert alert-danger text-center fw-bold">There were some issues saving your application, please see below.</div>
        @endif

        <form class="needs-validation" method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data">
            @include('forms.application')
        </form>
    </div>
@endsection
