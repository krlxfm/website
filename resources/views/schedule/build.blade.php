@extends('layouts.missioncontrol', ['title' => 'Schedule Builder - '.$term->name])

@push('css')
<link href="/css/fullcalendar.min.css" rel="stylesheet">
@endpush

@section('mid')
<schedule-builder v-bind:current-show-id="showID" v-bind:control-messages="controlMessages" v-on:current-show="setCurrentShow" v-on:remove-show="removeShow" v-on:publish="publish"></schedule-builder>
<schedule-publisher v-bind:diffs="diffs" v-on:draft="publishDraft" v-on:final="publishFinal"></schedule-publisher>
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
var tracks = {!! json_encode($tracks) !!};
var trackList = {!! json_encode($tracks->pluck('id')) !!};
var earlyClasses = {!! json_encode($early_classes) !!};
var term = {
    on_air: "{{ $term->on_air->format('Y-m-d H:i:s') }}",
    off_air: "{{ $term->off_air->format('Y-m-d H:i:s') }}"
}
</script>
@endpush
