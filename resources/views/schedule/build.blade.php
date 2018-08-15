@extends('layouts.missioncontrol', ['title' => 'Schedule Builder - '.$term->name])

@push('css')
<link href="/css/fullcalendar.min.css" rel="stylesheet">
@endpush

@section('mid')
<schedule-builder v-bind:current-show-id="showID" v-bind:control-messages="controlMessages" v-on:current-show="setCurrentShow" v-on:remove-show="removeShow"></schedule-builder>
@endsection

@push('topjs')
<script src="/js/schedule.js" defer></script>
@endpush

@push('js')
<script>
var showIDs = {!! json_encode(array_keys($shows)) !!};
var showList = {!! json_encode($shows) !!};
var specials = {!! json_encode(config('defaults.special_times')) !!};
var shows = showIDs.map(show => showList[show]);
var classTimes = {!! json_encode(config('classes.times')) !!};
</script>
@endpush
