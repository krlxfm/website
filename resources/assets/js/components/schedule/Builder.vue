<template>
<div class="form-row">
    <div class="col-md-3 d-none d-md-block">
        <schedule-queue v-bind:shows="shows" v-bind:length-colors="lengthColors" v-on:current-show="setCurrentShow"></schedule-queue>
    </div>
    <div class="col-md-6">
        <div class="card" style="border: 0">
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 d-none d-md-block">
        <div class="card mb-3 schedule-control-panel">
            <div class="card-body">
                <strong class="text-success"><i class="fas fa-check"></i> No errors here!</strong>
            </div>
        </div>
        <schedule-inspector v-bind:show="currentShow" v-bind:length-colors="lengthColors"></schedule-inspector>
    </div>
</div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            lengthColors: ['badge-light', 'badge-primary', 'badge-success', 'badge-warning', 'badge-danger'],
            shows: window.shows,
            currentShowID: ''
        }
    },
    computed: {
        currentShow: function() {
            return this.shows[window.showIDs.indexOf(this.currentShowID)];
        }
    },
    methods: {
        setCurrentShow: function(show) {
            this.currentShowID = show;
        }
    }
}
</script>
