<template>
    <div class="card schedule-full-height">
        <h5 class="card-header">Queue</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item schedule-queue-item" v-for="show in showsWithoutTimes" v-bind:data-show-id="show.id" v-bind:key="show.id" v-on:mousedown="$emit('current-show', show.id)">
                <h5 class="head-sans-serif mb-0">{{ show.title }}</h5>
                <p class="mb-0">
                    <span class="badge" v-bind:class="'bg-priority-'+show.priority_code.charAt(0).toLowerCase()">
                        <i class="fas fa-rocket" v-if="show.board_boost"></i>
                        {{ show.priority_code }}
                    </span>
                    <span class="badge bg-priority-a">
                        <i class="fas" v-bind:class="show.hosts.length == 1 ? 'fa-user' : 'fa-users'"></i>
                        {{ show.hosts.length }}
                    </span>
                    <span class="badge" v-bind:class="lengthColors[show.preferred_length / 30]">
                        <i class="fas fa-clock"></i>
                        {{ show.preferred_length }}m
                    </span>
                </p>
            </li>
        </ul>
    </div>
</template>

<script>
module.exports = {
    props: {
        lengthColors: Array,
        shows: Array
    },
    mounted: function () {
        window.enableDragging();
    },
    updated: function () {
        window.enableDragging();
    },
    computed: {
        showsWithoutTimes: function() {
            return this.shows.filter((show) => {
                return show.start == null || show.end == null || show.day == null;
            })
        }
    }
}
</script>
