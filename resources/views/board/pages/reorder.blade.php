@extends('layouts.missioncontrol', ['title' => 'Reorder Positions - Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <a href="{{ route('board.app', $app->year) }}"><i class="fas fa-chevron-left mr-1"></i>{{ $app->year . ' - ' . ($app->year + 1) }} BOARD APPLICATION</a>
            <h1 class="mb-3 mt-2">Reorder Positions</h1>
            <p>Use the "Move up" and "Move down" buttons to adjust the order shown below. Your first choice should be at the top of the list.</p>
            <form>
                @csrf
                <board-app-position-order class="mb-3"></board-app-position-order>
                <p>
                    <button type="submit" class="btn btn-lg btn-success">Save changes</button>
                </p>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
var positions = {!! json_encode($app->positions->map(function($position) { return $position->position; })) !!};
</script>
@endpush
