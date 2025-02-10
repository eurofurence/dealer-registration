@extends('layouts.app')
@section('content')
    <div class="px-4 py-5 my-5 text-center">
        <h1 class="display-5 fw-bold">Apply or join</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">
                Your can register as a dealer yourself, be invited by a dealer to either share a table or be an assistant to
                them.
            </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a target="_blank" href="{{ config('convention.dealers_tos_url') }}" class="text-secondary small">Rules and
                    Information</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title display-6">Apply as a dealer</h5>
                    <p class="card-text lead">As a dealer you are the main responsible person. If your space allows for it,
                        you may share your space with other dealers, or invite assistants to help you.</p>
                    <a href="{{ route('applications.create') }}" class="btn btn-lg btn-primary">Apply for a table</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title display-6">Join a dealer</h5>
                    <p class="card-text lead">A dealer may choose to invite you, invitations can be for two reasons either
                        as a assistant which is not selling items but rather working for a dealer or can choose to share
                        their space with another dealer.</p>
                    <a href="{{ route('join') }}" class="btn btn-lg btn-outline-primary">I have a invitation code</a>
                </div>
            </div>
        </div>
    </div>
@endsection
