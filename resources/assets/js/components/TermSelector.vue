<template>
    <div class="card my-3">
        <div class="card-body py-2">
            <form class="form-inline">
                <label class="my-1 mr-2" for="switchTerm">Switch to term:</label>
                <select class="custom-select my-1 mr-sm-2" id="switchTerm" name="newTerm" style="min-width: 50%" v-model="destination">
                    <option v-for="newTerm in termList" v-bind:value="newTerm.id">
                        {{ newTerm.name }}
                    </option>
                </select>
                <button type="button" class="btn btn-primary ml-auto my-1 px-5" v-on:click="switchTerms($event)">Go <i class="fas fa-chevron-right"></i></button>
            </form>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: {
        root: String,
        term: String,
    },
    data: function () {
        return {
            termList: window.terms,
            destination: this.term
        }
    },
    methods: {
        switchTerms: function(event) {
            event.preventDefault();
            console.log(this.destination);
            window.location.href = this.root + '/' + this.destination;
        }
    }
}
</script>
