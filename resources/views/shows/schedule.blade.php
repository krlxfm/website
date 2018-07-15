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

@push('js')
<script>
var classes = {!! json_encode($show->classes) !!};
var conflicts = {!! json_encode($show->conflicts) !!};
var preferences = {!! json_encode($show->preferences) !!};
var classTimes = {!! json_encode(config('classes.times')) !!};
</script>
<script src="/js/pages/shows/schedule.js"></script>
@endpush
