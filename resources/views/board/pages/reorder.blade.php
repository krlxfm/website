@extends('layouts.missioncontrol', ['title' => 'Reorder Positions - Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <a href="{{ route('board.app', $app->year) }}"><i class="fas fa-chevron-left mr-1"></i>{{ $app->year . ' - ' . ($app->year + 1) }} BOARD APPLICATION</a>
            <h1 class="mb-3 mt-2">Reorder Positions</h1>
        </div>
    </div>
@endsection
