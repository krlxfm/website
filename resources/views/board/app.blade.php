@extends('layouts.missioncontrol', ['title' => 'Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">{{ $app->year }} &ndash; {{ $app->year + 1 }} Board Application</h1>
            <p>Complete all parts of the Board Application as shown here (don't worry, they're each pretty short). Each panel will turn green when it has been completed. Once all panels are green, the "Submit" button will appear.</p>
            @if(count($missing_fields) == 0)
                <div class="card my-3">
                    <h5 class="card-header bg-success text-light">Profile</h5>
                    <div class="card-body">
                        <p>Candidates for the Board of Directors are required to have a little more information in their profiles &mdash; specifically, you'll need a bio, your hometown, your major (if you have one), and your pronouns.</p>
                        <p>We don't use this information to make decisions. Rather, this information will be published on the <a href="{{ route('board.meet') }}">Meet the Board</a> page in the event that you are elected.</p>
                        <a href="{{ route('profile') }}" class="card-link">Edit my profile</a>
                    </div>
                </div>
            @else
                <div class="card my-3">
                    <h5 class="card-header bg-warning"><i class="fas fa-exclamation-circle"></i> Profile</h5>
                    <div class="card-body">
                        <p class="card-text">Candidates for the Board of Directors are required to have a little more information in their profiles &mdash; specifically, you'll need a bio, your hometown, your major (if you have one), and your pronouns. Your profile is currently missing the following fields: {{ implode(', ', $missing_fields->all()) }}</p>
                        <p class="card-text">We don't use this information to make decisions, but if you are elected, this information will be published on the <a href="{{ route('board.meet') }}">Meet the Board</a> page.</p>
                        <a href="{{ route('profile') }}" class="card-link">Edit my profile</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
