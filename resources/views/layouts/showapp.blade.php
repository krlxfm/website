@extends('layouts.missioncontrol', ['title' => $title.' - '.$show->title])

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
