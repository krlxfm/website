@extends('layouts.missioncontrol', ['title' => 'Board Positions'])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">The Board Positions</h1>
            <p>Within the Board of Directors are several positions. Click on a role to see its full description and responsibilities. You can see who holds each role by <a href="{{ route('board.meet') }}">meeting the Board</a>.</p>
            @can('apply for board seats')
                <p>Do any of these look appealing to you? <a href="{{ route('board.index') }}">Click here to apply to all open seats.</a></p>
            @endcan
            <div id="positions-accordion">
            @forelse($positions as $position)
                <div class="card my-3">
                    <div class="card-header" style="background: {{ $position->color }}" id="head-{{ $position->abbr }}" data-toggle="collapse" data-target="#pos-{{ $position->abbr }}" aria-expanded="false" aria-controls="pos-{{ $position->abbr }}">
                        <h2 class="mb-0 text-{{ $position->dark ? 'light' : 'dark' }}">
                            {{ $position->title }}
                            <small>({{ $position->abbr }})</small>
                        </h2>
                    </div>
                    <div id="pos-{{ $position->abbr }}" class="collapse" aria-labelledby="head-{{ $position->abbr }}" data-parent="#positions-accordion">
                        <div class="card-body">
                            @if($position->on_call)
                                <div class="alert alert-warning">
                                    <i class="fas fa-phone fa-fw"></i> <strong>Critical On-Call Position</strong>
                                </div>
                            @endif
                            @if($position->restricted)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle fa-fw"></i> <strong>Additional eligibility requirements apply</strong>
                                </div>
                            @endif
                            {!! str_replace('<script>', '&lt;script&rt;', $position->description) !!}
                        </div>
                    </div>
                </div>
            @empty
                <p>No board positions have been imported yet. Check back later to learn about what positions are available and how to apply for them.</p>
            @endforelse
            </div>
        </div>
    </div>
@endsection
