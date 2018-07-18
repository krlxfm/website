@extends('layouts.missioncontrol', ['title' => 'Home'])

@section('head')
    <p>
        <a href="{{ route('shows.my') }}" class="btn btn-primary">My Shows</a>
        <a href="{{ route('shows.create') }}" class="btn btn-primary">Create new radio show</a>
    </p>
    <p>(Sorry this doesn't look super glamorous! It should look better soon!)</p>
    <p>You are currently testing on Beta 1 - v0.5.3 "Wilson IV" - <a href="https://github.com/krlxfm/website/releases/tag/v0.5.3">changelog</a></p>
@endsection
