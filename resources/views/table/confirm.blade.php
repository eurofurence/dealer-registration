@extends('layouts.app')
@section('title')
    Table confirmation
@endsection
@section('content')
    <div class="">
        <h1 class="text-center">Table Assignment and Dealer Package Details</h1>
        <div class="mx-auto text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
        </div>

        @if (Session::exists('table-confirmation-successful'))
            <div class="alert alert-success text-center fw-bold">Your package has been booked successfully. Thank you!</div>
        @elseif (Session::exists('table-confirmation-error'))
            <div class="alert alert-danger text-center fw-bold">An error has occurred, please try again. If the error
                persists, please get in touch with the Dealer's Den team at <a
                    href="mailto:{{ config('con.dealers_email') }}">{{ config('con.dealers_email') }}</a></div>
        @elseif (Session::exists('table-confirmation-registration-not-found'))
            <div class="alert alert-danger text-center fw-bold">We were unable to find your Eurofurence registration, which
                is a mandatory prerequisite for accepting a table at the Dealers' Den. If you have already
                registered for the convention, but the error persists, please get in touch with the Dealer's Den team at <a
                    href="mailto:{{ config('con.dealers_email') }}">{{ config('con.dealers_email') }}</a></div>
        @elseif (Session::exists('table-confirmation-registration-inactive'))
            <div class="alert alert-danger text-center fw-bold">Your registration for Eurofurence seems to be inactive, but
                accepting a table at the Dealers' Den requires an active registration for the event itself. Please check
                that your registration for the convention has been confirmed and has not been canceled, otherwise please get
                in touch with the Dealer's Den team at <a
                    href="mailto:{{ config('con.dealers_email') }}">{{ config('con.dealers_email') }}</a></div>
        @endif

        @if ($application->status === \App\Enums\ApplicationStatus::TableOffered)
            <form class="needs-validation" method="POST" action="{{ route('table.update') }}">
                @method('PUT')
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="px-4 text-center">
                            <div class="lead mb-4">
                                <p>
                                    Dear {{ $application->user()->first()->name }},
                                </p>
                                <p>
                                    It's our pleasure to inform you that your table assignment for the
                                    {{ config('con.con_name') }} Dealers' Den has been confirmed! We are excited to have
                                    you
                                    join us as a dealer at the convention!<br />
                                    Your table assignment details are as follows:
                                </p>
                                <p class="fw-bold">
                                    Table Number: {{ $application->table_number ?? $table_number }}<br />
                                    Requested Table Size: {{ $table_type_requested['name'] }}<br />
                                    Assigned Table Size: {{ $table_type_assigned['name'] }}
                                </p>
                                <p class="fw-bold">
                                    The final price for your dealer package is:
                                    {{ $table_type_assigned['price'] / 100 . ' EUR' }}
                                </p>
                                <p>
                                    Please review the details above and confirm your dealer package by clicking on the
                                    "Confirm Dealer Package" button below. By confirming your dealer package, you agree
                                    that
                                    the price will be added to your Eurofurence registration fee, and you will be
                                    required
                                    to pay this amount.
                                </p>
                                <p>
                                    If you wish to cancel your dealer application completely, please click on the
                                    "Cancel
                                    Dealer Application" button below. Please note that once you cancel your dealer
                                    application, it is not possible to re-activate it. If you have any questions or
                                    concerns
                                    about your table assignment or the payment process, please do not hesitate to
                                    contact us
                                    at <a
                                        href="mailto:{{ config('con.dealers_email') }}">{{ config('con.dealers_email') }}</a>.
                                    We are here to help ensure a smooth and enjoyable experience for all our dealers.
                                </p>
                                <p>
                                    Thank you once again for your interest in participating in
                                    {{ config('con.con_name') }}
                                    Dealers' Den. We hope to see you at the event, and we look forward to your continued
                                    support.
                                </p>
                            </div>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="{{ route('applications.delete') }}"
                                    class="btn btn-outline-danger btn-lg px-4">Cancel
                                    Dealer Application</a>
                                <button type="submit" class="btn btn-primary btn-lg px-4 gap-3">Confirm Dealer Package
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
