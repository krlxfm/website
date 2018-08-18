<template>
<div class="modal fade" id="publish" dusk="publish-modal" tabindex="-1" role="dialog" aria-labelledby="publish-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="publish-label">Publish Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Would you like to publish the following changes?</p>
                <ul>
                    <li v-for="(action, show) in diffs">
                        {{ showMessage(show, action) }}
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary ml-auto" v-on:click="$emit('draft')">
                    <i class="fas fa-cloud-upload-alt"></i> Publish Draft
                </button>
                <button type="button" class="btn btn-danger" v-on:click="$emit('final')">
                    <i class="fas fa-lock"></i> Publish &amp; Lock
                </button>
            </div>
        </div>
    </div>
</div>
</template>

<script>
module.exports = {
    props: {
        diffs: Object
    },
    methods: {
        showMessage: function(show, action) {
            console.log(window.shows);
                return "asdf";
        }
    }
}
</script>
