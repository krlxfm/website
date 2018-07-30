@extends('layouts.missioncontrol', ['title' => 'Join Show'])

@section('head')
    <div class="row my-3">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>Join Show</h1>
                <span class="d-none d-sm-block ml-auto mr-2">Show ID</span>
                <span class="d-none d-sm-block" style="font-size: xx-large; padding-bottom: 1px"><strong>{{ $show->id }}</strong></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body p-5 text-center">
                    <p>Would you like to join the {{ $show->track->name }}, {{ $show->term->name }} show</p>
                    <h3 class="head-sans-serif mb-3">
                        <strong>{{ $show->title }}</strong>
                    </h3>
                    <p class="mb-1">with
                        @switch($show->hosts->count())
                            @case(0)
                                literally nobody else?
                                @break
                            @case(1)
                                {{ $show->hosts->first()->full_name }}?
                                @break
                            @case(2)
                                {{ $show->hosts->first()->full_name }} and {{ $show->hosts->last()->full_name }}?
                                @break
                            @default
                                {{ $show->hosts->first()->full_name }} and {{ $show->hosts->count() - 1 }} others?
                        @endswitch
                    </p>
                    You'll have the opportunity to review and edit the full application, including schedule, after you accept.

                    <div class="mt-5 mb-2">
                        <button type="button" class="btn btn-lg btn-outline-danger">Decline invitation</button>
                        <button type="button" class="btn btn-lg btn-success">Accept invitation</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
