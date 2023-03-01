@extends('layouts.app')
@section('content')
    <div class="">
        <h1 class="text-center">Cancel your application</h1>

        @if(Session::exists('save-successful'))
            <div class="alert alert-success text-center fw-bold">Your data has been successfully saved.</div>
        @endif

        @if($application->type === \App\Enums\ApplicationType::Dealer)
            <div class="mx-auto text-center mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
            </div>
        @endif

        <form class="needs-validation" method="POST" action="{{ route('applications.destroy') }}">
            @method('DELETE')
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="px-4 py-5 my-5 text-center">
                        <h1 class="display-5 fw-bold">You are giving up your application!</h1>
                        <div class="col-lg-6 mx-auto">
                            <p class="lead mb-4">
                                Please note that this action means that you are giving your space to another dealer.
                                If a table has already been assigned to you, you will loose it.<br>
                                <b>Please only continue if you are sure that you want to cancel your application.</b>
                            </p>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <button type="submit" class="btn btn-danger btn-lg px-4 gap-3">Cancel Application</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">Keep my Application</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
