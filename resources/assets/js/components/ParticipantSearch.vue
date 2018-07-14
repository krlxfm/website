<template>
    <div>
        <input type="text" class="form-control" v-on:input="search">
        <div v-if="suggestions.length > 0" class="new-participant-suggestions">
        </div>
        <div v-else class="d-flex w-100 justify-content-center align-items-center new-participant-suggestions">
            <span v-if="noResults" style="margin: auto" class="text-center">No results matching {{ value }} were found</span>
            <span v-else style="margin: auto" class="text-center">Start typing in the field above to add a co-host</span>
        </div>
    </div>
</template>

<script>
module.exports = {
    data: function () {
        return {
            value: '',
            timer: null,
            noResults: false,
            suggestions: []
        }
    },
    methods: {
        search: function() {
            this.value = event.target.value;
            clearTimeout(this.timer);
            this.timer = setTimeout(this.sendSearch, 500);
        },
        sendSearch: function() {
            axios.get('/api/v1/users', { params: { q: this.value } })
            .then((response) => {
                console.log(response);
            })
        }
    }
}
</script>
