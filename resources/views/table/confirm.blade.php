@extends('layouts.app')
@section('title')
    Table confirmation
@endsection
@section('content')
    <div class="">
        <h1 class="text-center">Table confirmation</h1>
        <div class="mx-auto text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
        </div>

        @if (Session::exists('table-confirmation-successful'))
            <div class="alert alert-success text-center fw-bold">Your data has been saved successfully.</div>
        @elseif (Session::exists('table-confirmation-error'))
            <div class="alert alert-danger text-center fw-bold">An error has occurred.</div>
        @endif

        @if ($application->status === \App\Enums\ApplicationStatus::TableAccepted)
            <div class="card">
                <div class="card-body">
                    <div class="px-4 py-5 my-5 text-center">
                        <h1 class="display-5 fw-bold">Table accepted - thank you!</h1>
                        <div class="col-lg-6 mx-auto">
                            <p class="lead mb-4">
                                <b>Your table:</b>
                                <br />Table number: {{ $application->table_number ?? $table_number }}
                                <br />Table type:
                                {{ $table_type_assigned['name'] . ' with ' . $table_type_assigned['seats'] . ' seat(s)' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($application->status === \App\Enums\ApplicationStatus::TableOffered)
            <form class="needs-validation" method="POST" action="{{ route('table.update') }}">
                @method('PUT')
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="px-4 py-5 my-5 text-center">
                            <h1 class="display-5 fw-bold">You have been assigned a table!</h1>
                            <div class="col-lg-6 mx-auto">
                                <p class="lead mb-4">
                                    <b>Please note that by confirming your table you will be billed.</b>

                                    <br />Table number: {{ $application->table_number ?? $table_number }}

                                    <br />Requested:
                                    {{ $table_type_requested['name'] . ' with ' . $table_type_requested['seats'] . ' seat(s)' }}

                                    <br />Assigned:
                                    {{ $table_type_assigned['name'] . ' with ' . $table_type_assigned['seats'] . ' seat(s)' }}

                                    <br /><b>Price: {{ $table_type_assigned['price'] / 100 . ' EUR' }}</b>

                                </p>
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                    <a href="{{ route('dashboard') }}"
                                        class="btn btn-outline-secondary btn-lg px-4">Cancel</a>
                                    <button type="submit" class="btn btn-danger btn-lg px-4 gap-3">Confirm table
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
