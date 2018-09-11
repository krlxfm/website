@extends('layouts.missioncontrol', ['title' => 'Unauthorized'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>Action Forbidden</h1>

            <p>You attempted to take a prohibited action or visit a page which you do not have access to. This is likely because the page you attempted to view is restricted. Not to worry though, no data has been affected.</p>
            <p>If you believe this is an error, please contact an IT engineer for assistance.</p>
            <h3>Back to safety</h3>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shows.my') }}">My shows</a></li>
            </ul>
        </div>
    </div>
@endsection
