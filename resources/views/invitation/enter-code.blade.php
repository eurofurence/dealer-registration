@extends('layouts.app')
@section('content')
    <div class="">
        <div class="col-md-6 mx-auto">
            <h1 class="text-center">Join another dealer</h1>
            <p class="text-center lead">If you have been given an Invite Code, you can enter it here to join another dealer.
                Depending on the type of code entered, you will be added as another dealer or as an assistant.<br>
                Please note that you can only have one application. If you already applied as a regular dealer, you will loose your space when joining another dealer as you will share their space.
            </p>
        </div>

        @if(Session::exists('save-successful'))
            <div class="alert alert-success text-center fw-bold">Your data has been successfully saved.</div>
        @endif

        <div class="mx-auto text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">Return to dashboard</a>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="px-4 py-5 my-5 text-center">
                    <form action="{{ route('join.submit') }}" method="post">
                        @csrf
                        <div class="col-md-6 mx-auto">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" placeholder="Invite Code">
                                <button class="btn btn-primary" type="submit" id="button-addon2">Submit
                                </button>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
