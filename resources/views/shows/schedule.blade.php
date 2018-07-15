@extends('layouts.showapp', ['title' => 'Schedule', 'back' => 'content'])

@section('head')
    @parent
    <div class="row">
        <div class="col">
            <form method="post" action="/shows/{{ $show->id }}" id="content-form">
                @method('patch')
                @csrf
                @if($show->track->weekly)
                    @include('shows.weekly')
                @endif
            </form>
        </div>
    </div>
@endsection
