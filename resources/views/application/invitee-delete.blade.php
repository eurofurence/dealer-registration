@extends('layouts.app')
@section('title')
    Remove {{ ucfirst($invitee->type->value) }} from your Dealership
@endsection
@section('content')
    <div class="">
        <h1 class="text-center">@yield('title')</h1>

        <div class="mx-auto text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to Dashboard</a>
        </div>

        <form class="needs-validation" method="POST" action="{{ route('applications.invitees.destroy') }}">
            @method('DELETE')
            @csrf
            <input type="hidden" name="invitee_id" value="{{ $invitee->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="px-4 py-5 my-5 text-center">
                        <h1 class="display-5 fw-bold">You are removing <span class="text-nowrap text-primary">{{ $invitee->user->name }}</span> from your Dealership!</h1>
                        <div class="col-lg-6 mx-auto">
                            <p class="lead mb-4">
                                This action will cause them to be completely removed as a {{ ucfirst($invitee->type->value) }} from your dealership.<br>
                                They will be notified of this.
                            </p>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="{{ route('dashboard') }}"
                                   class="btn btn-outline-secondary btn-lg px-4">
                                    Stop! Take me back!
                                </a>
                                <button type="submit" class="btn btn-danger btn-lg px-4 gap-3">
                                    Yes, remove this {{ ucfirst($invitee->type->value) }} from my Dealership
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
