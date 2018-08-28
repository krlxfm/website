<template>
<div>
    <div class="modal fade" id="publish" dusk="publish-modal" tabindex="-1" role="dialog" aria-labelledby="publish-label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publish-label">Publish Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-if="diffsToPublish">
                    <p>Would you like to publish the following changes?</p>
                    <ul>
                        <li v-for="(action, show) in diffs">
                            {{ getTitle(show) }}:
                            <span v-if="action == 'new'">
                                <strong class="text-success">Publish</strong> on {{ getWorkingTime(show) }}
                            </span>
                            <span v-else-if="action == 'rm'">
                                <strong class="text-danger">Remove</strong> from {{ getPublishedTime(show) }}
                            </span>
                            <span v-else-if="action == 'mv'">
                                <strong class="text-primary">Time change</strong> from {{ getPublishedTime(show) }} to {{ getWorkingTime(show) }}
                            </span>
                            <span v-else>
                                <strong>Do other things</strong> (hey, this is a bug, please open an issue)
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="modal-body" v-else>
                    <p><strong>The current schedule is up to date and there are no new changes to publish.</strong></p>
                    You can still lock the schedule if you are ready to stop accepting further changes.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ml-auto" v-on:click="$emit('draft')" v-if="diffsToPublish">
                        <i class="fas fa-cloud-upload-alt"></i> Publish Draft
                    </button>
                    <button type="button" class="btn btn-danger" v-on:click="$emit('final')">
                        <i class="fas fa-lock"></i> Publish &amp; Lock
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="publishStatus" dusk="publishStatus-modal" tabindex="-1" role="dialog" aria-labelledby="publishStatus-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publishStatus-label">Publication in progress</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ currentItem }}</p>
                    <div class="progress">
                        <div class="progress-bar" id="publish-progress-bar" role="progressbar" v-bind:style="{ width: (100 * progress / (1 + Object.keys(diffs).length)) + '%' }" v-bind:aria-valuenow="progress" aria-valuemin="0" v-bind:aria-valuemax="1 + Object.keys(diffs).length"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="publisher-pubInProgressButton" class="btn btn-light" disabled>Publishing...</button>
                    <button type="button" id="publisher-pubDoneButton" class="btn btn-secondary" data-dismiss="modal">Finish</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
module.exports = {
    props: {
        diffs: Object,
        currentItem: String,
        progress: Number
    },
    computed: {
        diffsToPublish: function() {
            return Object.keys(this.diffs).length > 0;
        }
    },
    methods: {
        getShow: function(show) {
            return window.showList[show];
        },
        getTitle: function(show) {
            return this.getShow(show).title;
        },
        getWorkingTime: function(show) {
            const data = this.getShow(show);
            return this.showTime(data.day, data.start, data.end);
        },
        getPublishedTime: function(show) {
            const data = this.getShow(show);
            return this.showTime(data.published_day, data.published_start, data.published_end);
        },
        showTime(day, start, end) {
            const today = moment().format('YYYY-MM-DD');
            return day + ', ' + moment(today + ' ' + start + ':00').format('h:mm a') + ' - ' + moment(today + ' ' + end + ':00').format('h:mm a');
        }
    }
}
</script>
