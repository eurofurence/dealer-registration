@extends('layouts.app')
@section('title')
    Cancel your application
@endsection
@section('content')
    <div class="">
        <h1 class="text-center">Cancel your application</h1>

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
                        @if($application->type === \App\Enums\ApplicationType::Dealer)
                            <h1 class="display-5 fw-bold">You are giving up your application!</h1>
                            <div class="col-lg-6 mx-auto">
                                <p class="lead mb-4">
                                    Please note that this action means that you are giving your space to another dealer.
                                    If a table has already been assigned to you, you will lose it.<br>
                                    <b>Please only continue if you are sure that you want to cancel your application.</b>
                                </p>
                                <p class="text-muted">
                                    Should you change your mind afterwards, you can resubmit your application or join another dealership until the end of the application phase.
                                </p>
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                    <a href="{{ route('dashboard') }}"
                                       class="btn btn-outline-secondary btn-lg px-4">Keep my Application</a>
                                    <button type="submit" class="btn btn-danger btn-lg px-4 gap-3">Cancel Application
                                    </button>
                                </div>
                            </div>
                            @else

                            <h1 class="display-5 fw-bold">You are leaving another dealer!</h1>
                            <div class="col-lg-6 mx-auto">
                                <p class="lead mb-4">
                                    This action will cause you to be completely removed as {{ $application->type }} from the dealership.
                                    The owner of the dealership will be notified that you have left their application.
                                </p>
                                <p>
                                    You may choose to apply for a dealership of your own or join another dealership after you left.
                                </p>
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                    <button type="submit" class="btn btn-danger btn-lg px-4 gap-3">Leave Dealership
                                    </button>
                                    <a href="{{ route('dashboard') }}"
                                       class="btn btn-outline-secondary btn-lg px-4">Stay in Dealership</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
