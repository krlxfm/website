@extends('layouts.showapp', ['title' => 'Review', 'back' => 'schedule'])

@section('head')
    @parent
    <div class="row">
        <div class="col">
            @if($show->submitted)
                <div class="alert alert-success">
                    <strong>This application has been submitted for scheduling!</strong> You can continue to edit it until applications close.
                </div>
            @else
                <div class="alert alert-warning">
                    <strong>This application has not yet been submitted for scheduling.</strong>
                </div>
            @endif
            @if($show->track->weekly)
        </div>
    </div>
    <div class="row">
        <div class="col col-md-9 col-lg-10">
            <h2 class="d-none d-md-block mb-3">Application</h2>
            @endif
            @include('shows.review-table')
            @if($show->track->weekly)
            </div>
            <div class="d-none d-md-block col-md-3 col-lg-2">
                <h2>Schedule</h2>
                <schedule-preview></schedule-preview>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            <p><a href="{{ route('shows.delete', $show) }}" class="btn btn-outline-danger">Delete {{ $show->title }}</a></p>
        </div>
    </div>
@endsection

@section('next')
@if($show->submitted)
<button class="btn btn-light" type="button" disabled>
    Submitted! <i class="fas fa-check"></i>
</button>
@else
<button class="btn btn-success" onclick="reviewAndSubmit()" type="button">
    Submit <i class="fas fa-chevron-right"></i>
</button>
@endif
@endsection

@push('js')

<script>
var showID = "{{ $show->id }}";
@if($show->track->weekly)
var conflicts = {!! json_encode($show->conflicts) !!};
var preferences = {!! json_encode($show->preferences) !!};
var classes = {!! json_encode($show->classes) !!};
var classTimes = {!! json_encode(config('classes.times')) !!};
@endif
</script>
@if($show->track->weekly)
<script src="/js/pages/shows/preview.js" defer></script>
@endif
<script src="/js/pages/shows/review.js" defer></script>
@endpush
