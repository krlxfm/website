@extends('layouts.missioncontrol', ['title' => 'Home'])

@section('head')
    <div class="row">
        <div class="d-none d-md-block col-md-3 text-center">
            <img src="{{ $user->photo }}" style="border-radius: 3rem;" class="mb-2" width="100%">
            {{ $user->email }}
        </div>
        <div class="col col-md-9">
            <h1>Welcome back, {{ $user->first_name }}</h1>
            <p>You are currently testing on Beta 1 - v0.6.0 "Lincoln" - <a href="https://github.com/krlxfm/website/releases/tag/v0.6.0">changelog</a></p>
        </div>
    </div>
@endsection
