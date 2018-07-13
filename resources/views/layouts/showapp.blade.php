@extends('layouts.missioncontrol', ['title' => $title.' - '.$show->title, 'body_class' => 'has-page-foot'])

@section('head')
    <div class="row my-3">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>{{ $title }}</h1>
                <span class="ml-auto mr-2">Show ID</span>
                <span style="font-size: xx-large; padding-bottom: 1px"><strong>{{ $show->id }}</strong></span>
            </div>
        </div>
    </div>
@endsection

@section('bottom')
    <div class="mc-toolbar-footer">
        <div class="container d-flex">
            <a href="{{ route("shows.$next", $show) }}" class="btn btn-primary ml-auto" id="next-button" dusk="next">Next: {{ ucwords($next) }} <i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
@endsection
