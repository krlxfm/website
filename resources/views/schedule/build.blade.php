@extends('layouts.missioncontrol', ['title' => 'Schedule Builder - '.$term->name])

@push('css')
<link href="/css/fullcalendar.min.css" rel="stylesheet">
@endpush

@section('mid')
<schedule-builder v-bind:current-show-id="showID" v-bind:control-messages="controlMessages" v-on:current-show="setCurrentShow" v-on:remove-show="removeShow" v-on:publish="publish" v-on:sync-changes="syncChanges"></schedule-builder>
<schedule-publisher v-bind:current-item="currentItem" v-bind:diffs="diffs" v-bind:progress="progress" v-on:draft="publishDraft" v-on:final="publishFinal"></schedule-publisher>
<div class="modal fade" id="syncing-modal" dusk="syncing-modal" tabindex="-1" role="dialog" aria-labelledby="syncing-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncing-modal-label">Sync in progress</h5>
            </div>
            <div class="modal-body">
                <p><strong>Your changes are currently being synchronized to the server.</strong></p>
                This message will disappear automatically once all changes are saved. If it's still visible for more than a few seconds, refresh the page.
            </div>
        </div>
    </div>
</div>
@endsection

@push('topjs')
<script src="/js/schedule_19wi.js" defer></script>
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
    id: "{{ $term->id }}",
    on_air: "{{ $term->on_air->format('Y-m-d H:i:s') }}",
    off_air: "{{ $term->off_air->format('Y-m-d H:i:s') }}"
}
</script>
@endpush
