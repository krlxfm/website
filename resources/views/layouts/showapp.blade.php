@extends('layouts.missioncontrol', ['title' => $title.' - '.$show->title, 'body_class' => 'has-page-foot'])

@section('head')
    <div class="row">
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
            <a href="#" class="btn btn-primary ml-auto">Next: {{ ucwords($next) }} <i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
@endsection
