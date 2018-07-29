<template>
    <div class="card my-3">
        <div class="card-body py-2">
            <form class="form-inline">
                <label class="my-1 mr-2" for="switchTerm">Switch to term:</label>
                <select class="custom-select my-1 mr-sm-2" id="switchTerm" name="newTerm">
                    <option v-for="newTerm in termList" v-bind:value="newTerm.id">
                        A
                    </option>
                </select>
                <button type="submit" class="btn btn-primary my-1">Go</button>
            </form>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: {
        root: String,
        term: String
    },
    data: function () {
        return {
            termList: window.terms
        }
    }
}
</script>
