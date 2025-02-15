@extends('layouts.app')

@section('title')
    Closed
@endsection
@section('content')
    <div class="px-4 py-3 my-3 text-center">
        <h1 class="display-5 fw-bold">Thank you!</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">
                We appreciate your interest in the Eurofurence Dealers' Den and would love to see you again during our next
                convention!
            </p>
            <p class="lead mb-4">
                Make sure to keep an eye out on our social media channels to stay in the loop about the latest news on Eurofurence and our Dealers' Den:
                <ul class="list-group">
                    <li class="list-group-item"><a href="https://www.eurofurence.org/" target="_blank"><x-heroicon-o-globe-alt style="height: 1lh;" /> Eurofurence Website</a></li>
                    <li class="list-group-item"><a href="https://www.twitter.com/eurofurence" target="_blank"><x-heroicon-o-megaphone style="height: 1lh;" /> &commat;eurofurence on Twitter <span class="small">(currently known as X)</span></a></li>
                    <li class="list-group-item"><a href="https://t.me/efnotifications" target="_blank"><x-heroicon-m-paper-airplane style="height: 1lh;" /> &commat;efnotifications on Telegram</a></li>
                </ul>
            </p>
        </div>
    </div>
@endsection
