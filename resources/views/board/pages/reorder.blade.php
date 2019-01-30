@extends('layouts.missioncontrol', ['title' => 'Reorder Positions - Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <a href="{{ route('board.app', $app->year) }}"><i class="fas fa-chevron-left mr-1"></i>{{ $app->year . ' - ' . ($app->year + 1) }} BOARD APPLICATION</a>
            <h1 class="mb-3 mt-2">Reorder Positions</h1>
            @if($app->submitted)
                <div class="alert alert-warning">
                    Your application has been submitted. You can review your position preference below, though changes will not be saved.
                </div>
            @else
                <p>Use the "Move up" and "Move down" buttons to adjust the order shown below. Your first choice should be at the top of the list.</p>
            @endif
            <form method="post" action="{{ route('board.reorder', $app->year) }}">
                @csrf
                @method('PATCH')
                <board-app-position-order class="mb-3"></board-app-position-order>
                @unless($app->submitted)
                    <p>
                        <button type="submit" class="btn btn-lg btn-success">Save changes</button>
                    </p>
                @endunless
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
var positions = {!! json_encode($app->positions->map(function($position) { return $position->position; })) !!};
</script>
@endpush
