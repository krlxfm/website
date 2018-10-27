@extends('layouts.missioncontrol', ['title' => 'Common Questions - Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <a href="{{ route('board.app', $app->year) }}"><i class="fas fa-chevron-left mr-1"></i>{{ $app->year . ' - ' . ($app->year + 1) }} BOARD APPLICATION</a>
            <h1 class="mb-3 mt-2">Common Questions</h1>
            <form method="post" action="{{ route('board.app', $app->year) }}">
                @method('patch')
                @csrf
                @foreach($app->common as $question => $answer)
                    <p>{{ $question }}</p>
                @endforeach
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="/js/pages/board/logistics.js" defer></script>
@endpush
