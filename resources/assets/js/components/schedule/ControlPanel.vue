<template>
    <div class="card mb-3 schedule-control-panel">
        <div class="card-body pt-3 d-flex flex-column align-items-stretch">
            <template v-if="messages.errors && messages.errors.length > 0">
                <p class="mb-0"><strong class="text-danger"><i class="fas fa-times"></i> Error:</strong> {{ messages.errors[0].message }}</p>
                <button type="button" class="btn btn-dark btn-block mt-auto" v-if="messages.errors[0].fixable">
                    <i class="fas fa-wrench"></i> Auto-Fix
                </button>
                <button type="button" disabled class="btn btn-dark btn-block mt-auto" v-else>
                    <i class="fas fa-times"></i> Fix manually before publishing
                </button>
            </template>
            <template v-else-if="messages.warnings && messages.warnings.length > 0">
                <p class="mb-0"><strong><i class="fas fa-exclamation-triangle text-warning"></i> Careful:</strong> {{ messages.warnings[0].message }}</p>
                <div class="form-row mt-auto">
                    <div class="col" v-if="messages.warnings[0].fixable">
                        <button type="button" class="btn btn-dark btn-block mt-auto" v-if="messages.warnings[0].fixable">
                            <i class="fas fa-wrench"></i> Fix
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-warning btn-block mt-auto">
                            <i class="fas fa-cloud-upload-alt"></i> Publish anyway
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
                        <button type="button" class="btn btn-info btn-block mt-auto">
                            <i class="fas fa-cloud-upload-alt"></i> Publish
                        </button>
                    </div>
                </div>
            </template>
            <template v-else>
                <p class="mb-0"><strong class="text-success"><i class="fas fa-check"></i> All good!</strong> Ready to publish.</p>
                <button type="button" class="btn btn-success btn-block mt-auto">
                    <i class="fas fa-cloud-upload-alt"></i> Publish
                </button>
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
