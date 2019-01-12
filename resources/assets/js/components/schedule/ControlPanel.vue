<template>
    <div class="card mb-3 schedule-control-panel">
        <div class="card-body pt-3 d-flex flex-column align-items-stretch">
            <template v-if="messages.errors && messages.errors.length > 0">
                <p class="mb-0"><strong class="text-danger"><i class="fas fa-times"></i> Error:</strong> {{ messages.errors[0].message }}</p>
                <div class="form-row mt-auto">
                    <div class="col">
                        <button type="button" class="btn btn-dark btn-block mt-auto" v-on:click="$emit('sync-changes')">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" disabled class="btn btn-dark btn-block mt-auto">
                            <i class="fas fa-times"></i> Can't publish!
                        </button>
                    </div>
                </div>
            </template>
            <template v-else-if="messages.warnings && messages.warnings.length > 0">
                <p class="mb-0"><strong><i class="fas fa-exclamation-triangle text-warning"></i> Careful:</strong> {{ messages.warnings[0].message }}</p>
                <div class="form-row mt-auto">
                    <div class="col">
                        <button type="button" class="btn btn-dark btn-block mt-auto" v-on:click="$emit('sync-changes')">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-warning btn-block mt-auto" v-on:click="$emit('publish')">
                            <i class="fas fa-cloud-upload-alt"></i> Publish
                        </button>
                    </div>
                </div>
            </template>
            <template v-else-if="messages.suggestions && messages.suggestions.length > 0">
                <p class="mb-0"><strong class="text-info"><i class="fas fa-info"></i> Looks pretty good, but:</strong> {{ messages.suggestions[0].message }}</p>
                <div class="form-row mt-auto">
                    <div class="col" v-if="messages.suggestions[0].fixable">
                        <button type="button" class="btn btn-dark btn-block mt-auto" v-if="messages.suggestions[0].fixable">
                            <i class="fas fa-wrench"></i> Fix
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-dark btn-block mt-auto" v-on:click="$emit('sync-changes')">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-info btn-block mt-auto" v-on:click="$emit('publish')">
                            <i class="fas fa-cloud-upload-alt"></i> Publish
                        </button>
                    </div>
                </div>
            </template>
            <template v-else>
                <p class="mb-0"><strong class="text-success"><i class="fas fa-check"></i> All good!</strong> Ready to publish.</p>
                <div class="form-row mt-auto">
                    <div class="col">
                        <button type="button" class="btn btn-dark btn-block mt-auto" v-on:click="$emit('sync-changes')">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success btn-block mt-auto" v-on:click="$emit('publish')">
                            <i class="fas fa-cloud-upload-alt"></i> Publish
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: {
        messages: Object
    }
}
</script>
