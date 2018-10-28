@extends('layouts.missioncontrol', ['title' => 'Board Positions'])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">The Board Positions</h1>
            <p>Within the Board of Directors are several positions. This is the list of each role and its full responsibilities.</p>
            <div id="positions-accordion">
            @foreach($positions as $position)
                <div class="card my-3">
                    <div class="card-header" style="background: {{ $position->color }}" id="head-{{ $position->abbr }}" data-toggle="collapse" data-target="#pos-{{ $position->abbr }}" aria-expanded="false" aria-controls="pos-{{ $position->abbr }}">
                        <h2 class="mb-0 text-{{ $position->dark ? 'light' : 'dark' }}">
                            {{ $position->title }}
                            <small>({{ $position->abbr }})</small>
                        </h2>
                    </div>
                    <div id="pos-{{ $position->abbr }}" class="collapse" aria-labelledby="head-{{ $position->abbr }}" data-parent="#positions-accordion">
                        <div class="card-body">
                            {!! str_replace('<script>', '&lt;script&rt;', $position->description) !!}
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
@endsection
