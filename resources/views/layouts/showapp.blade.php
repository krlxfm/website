@extends('layouts.missioncontrol', ['title' => $title.' - '.$show->title, 'body_class' => 'has-page-foot'])

@section('head')
    <div class="row my-3">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>{{ $title }}</h1>
                <span class="d-none d-sm-block ml-auto mr-2">Show ID</span>
                <span class="d-none d-sm-block" style="font-size: xx-large; padding-bottom: 1px"><strong>{{ $show->id }}</strong></span>
            </div>
        </div>
    </div>
@endsection

@section('bottom')
    <div class="mc-toolbar-footer">
        <div class="container d-flex">
            <div class="d-flex">
                @isset($back)
                    <a href="{{ route("shows.$back", $show) }}" class="btn btn-primary" id="back-button" dusk="next"><i class="fas fa-chevron-left"></i> Back: {{ ucwords($back) }}</a>
                @endisset
                @yield('back')
            </div>
            <div class="ml-auto d-flex align-items-center">
                <span class="mr-2" id="changes-saved-item">
                    <i class="fas fa-check text-success"></i>
                    Changes saved!
                </span>
                @yield('next')
                @isset($next)
                    <button type="button" data-destination="{{ route("shows.$next", $show) }}" class="btn btn-primary" id="next-button" dusk="next" onclick="window.location.href = $(this).data('destination')">Next: {{ ucwords($next) }} <i class="fas fa-chevron-right"></i></button>
                @endisset
            </div>
        </div>
    </div>
@endsection
