@extends('layouts.missioncontrol', ['title' => 'My Account'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>{{ $user->name }}</h1>
        </div>
    </div>
@endsection
