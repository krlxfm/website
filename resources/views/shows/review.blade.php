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
            <table class="table table-responsive-sm">
                <thead>
                    <tr>
                        <th>Attribute</th>
                        <th>Value</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @include('shows.tr', ['field' => 'title', 'title' => $show->track->title_label ?? 'Title', 'value' => $show->title, 'path' => 'content'])

                    <tr>
                        <td>Status</td>
                        <td>
                            @if($show->submitted)
                                Submitted &mdash; priority {{ $show->priority }}
                                @if($show->boosted)
                                    <span class="badge badge-danger"><i class="fas fa-rocket"></i> PRIORITY BOOST</span>
                                @endif
                            @else
                                Submission pending
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Last updated</td>
                        <td>{{ $show->updated_at->format('F j, Y, g:i:s a') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Track and term</td>
                        <td>{{ $show->track->name }}, {{ $show->term->name }}</td>
                        <td></td>
                    </tr>

                    @include('shows.tr', ['field' => 'hosts', 'title' => 'Hosts', 'value' => $show->hosts->pluck('full_name')->all(), 'path' => 'hosts'])
                    @include('shows.tr', ['field' => 'invitees', 'title' => 'Invitees', 'value' => $show->invitees->pluck('full_name')->all(), 'path' => 'hosts'])

                    @include('shows.tr', ['field' => 'description', 'title' => 'Description', 'value' => $show->description, 'path' => 'content'])
                    @foreach($show->track->content as $field)
                        @include('shows.tr', ['field' => 'content.'.$field['db'], 'title' => $field['title'], 'value' => $show->content[$field['db']], 'path' => 'content'])
                    @endforeach

                    @if($show->track->weekly)
                        @include('shows.tr', ['field' => 'preferred_length', 'title' => 'Preferred length', 'value' => "{$show->preferred_length} minutes", 'path' => 'schedule'])

                        @php
                        $special_indicators = ['y' => 'Yes, please try to schedule me here', 'n' => 'No thanks, please avoid scheduling me here', 'm' => 'Meh, doesn\'t matter'];
                        @endphp
                        @foreach(config('defaults.special_times') as $id => $zone)
                            @include('shows.tr', ['field' => 'special_times.'.$id, 'title' => $zone['name'], 'value' => $special_indicators[$show->special_times[$id]], 'path' => 'schedule'])
                        @endforeach

                        @include('shows.tr', ['field' => 'classes', 'title' => 'Classes', 'value' => implode(', ', $show->classes), 'path' => 'schedule'])
                        @include('shows.schedule-tr', ['field' => 'conflicts', 'title' => 'Conflicts', 'list' => $show->conflicts, 'path' => 'schedule'])
                        @include('shows.schedule-tr', ['field' => 'preferences', 'title' => 'Preferences', 'list' => $show->preferences, 'path' => 'schedule'])
                    @else
                        @include('shows.tr', ['field' => 'conflicts', 'title' => 'Conflicts', 'value' => implode(', ', array_map(function($conflict) { return Carbon\Carbon::parse($conflict)->format('F j'); }, $show->conflicts)), 'path' => 'schedule'])
                        @include('shows.tr', ['field' => 'preferences', 'title' => 'Preferences', 'value' => implode(', ', array_map(function($preference) { return Carbon\Carbon::parse($preference)->format('F j'); }, $show->preferences)), 'path' => 'schedule'])
                    @endif
                    @foreach($show->track->scheduling as $field)
                        @include('shows.tr', ['field' => 'scheduling.'.$field['db'], 'title' => $field['title'], 'value' => $show->scheduling[$field['db']], 'path' => 'schedule'])
                    @endforeach
                    @include('shows.tr', ['field' => 'notes', 'title' => 'Scheduling notes', 'value' => $show->notes, 'path' => 'schedule'])
                </tbody>
            </table>
            @if($show->track->weekly)
            </div>
            <div class="d-none d-md-block col-md-3 col-lg-2">
                <h2>Schedule</h2>
                <schedule-preview></schedule-preview>
            @endif
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
