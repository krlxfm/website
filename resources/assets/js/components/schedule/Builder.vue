<template>
<div class="form-row">
    <div class="col-md-3 d-none d-md-block">
        <schedule-queue v-bind:shows="shows" v-bind:length-colors="lengthColors" v-on:current-show="$emit('current-show', $event)"></schedule-queue>
    </div>
    <div class="col-md-6">
        <div class="card" style="border: 0">
            <div class="card-body p-0" id="schedule-builder-parent">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 d-none d-md-block">
        <schedule-control-panel v-bind:messages="controlMessages"></schedule-control-panel>
        <schedule-inspector v-bind:show="currentShow" v-bind:length-colors="lengthColors" v-on:remove-show="$emit('remove-show')"></schedule-inspector>
    </div>
</div>
</template>

<script>
module.exports = {
    props: {
        currentShowId: String,
        controlMessages: Object
    },
    data: function() {
        return {
            lengthColors: ['badge-light', 'badge-primary', 'badge-success', 'badge-warning', 'badge-danger'],
            shows: window.shows,
            tracks: window.tracks
        }
    },
    computed: {
        currentShow: function() {
            if(this.currentShowId.includes('-')) {
                const realTrackID = parseInt(this.currentShowId.split('-')[1]);
                return this.tracks[window.trackList.indexOf(realTrackID)];
            } else {
                return this.shows[window.showIDs.indexOf(this.currentShowId)];
            }
        }
    }
}
</script>
