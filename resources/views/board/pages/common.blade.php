@extends('layouts.missioncontrol', ['title' => 'Common Questions - Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <a href="{{ route('board.app', $app->year) }}"><i class="fas fa-chevron-left mr-1"></i>{{ $app->year . ' - ' . ($app->year + 1) }} BOARD APPLICATION</a>
            <h1 class="mb-3 mt-2">Common Questions</h1>
            <form method="post" action="{{ route('board.app', $app->year) }}">
                @method('patch')
                @csrf
                @foreach($app->common as $question => $response)
                    <h5 class="head-sans-serif mb-1 mt-3"><strong>{{ $question }}</strong></h5>
                    <textarea name="common[{{ $question }}]">{!! $response !!}</textarea>
                @endforeach
                <div class="my-3">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Save and continue</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('postjs')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey={{ env('TINYMCE_API_KEY', 'asdf') }}" defer></script>
@endpush

@push('js')
<script src="/js/pages/board/common.js" defer></script>
@endpush
